<?php

namespace Pipa\Data\Source\MySQL;
use DateTime;
use mysqli;
use mysqli_result;
use Pipa\Data\Collection;
use Pipa\Data\Exception\AuthException;
use Pipa\Data\Exception\ConstraintException;
use Pipa\Data\Exception\DataException;
use Pipa\Data\Exception\DuplicateEntryException;
use Pipa\Data\Exception\QuerySyntaxException;
use Pipa\Data\Exception\UnknownFieldException;
use Pipa\Data\Exception\UnknownHostException;
use Pipa\Data\Exception\UnknownSchemaException;
use Pipa\Data\Query\Aggregate;
use Pipa\Data\Query\Criteria;
use Pipa\Data\Relational\RelationalCriteria;
use Pipa\Data\Source\DataSource;
use Pipa\Data\Source\MultipleInsertionSupport;
use Pipa\Data\Source\StoredProcedureSupport;
use Pipa\Data\Source\TransactionalDataSource;
use Pipa\Data\SQL\BasicSQLQuerying;
use Pipa\Data\SQL\SQLBuffer;
use Pipa\Data\SQL\SQLDataSource;

class MySQLDataSource implements DataSource, SQLDataSource, TransactionalDataSource,
    MultipleInsertionSupport, StoredProcedureSupport {
    use BasicSQLQuerying;

    const TYPE_TINYINT = 1;
    const TYPE_SMALLINT = 2;
    const TYPE_MEDIUMINT = 9;
    const TYPE_INT = 3;
    const TYPE_BIGINT = 8;
    const TYPE_DECIMAL = 246;
    const TYPE_FLOAT = 4;
    const TYPE_DOUBLE = 5;
    const TYPE_BIT = 16;
    const TYPE_DATE = 10;
    const TYPE_DATETIME = 12;
    const TYPE_TIMESTAMP = 7;
    const TYPE_TIME = 11;
    const TYPE_YEAR = 13;
    const TYPE_CHAR = 254;
    const TYPE_VARCHAR = 253;
    const TYPE_TEXT = 252;

    protected $connection;

    protected $generator;

    protected $logger;

    function __construct($db, $host = "localhost", $user = "root", $password = "", array $options = []) {
        $this->connection = @new mysqli("p:$host", $user, $password, $db);

        if (!$this->connection->connect_errno) {
            $this->generator = new MySQLGenerator($this);
            $this->connection->set_charset("utf8");
			$this->connection->autocommit(true);
        } else {
            throw $this->translateException($this->connection->connect_errno, $this->connection->connect_error);
        }
    }

    function aggregate(Aggregate $aggregate, Criteria $criteria) {
        $sql = $this->generator->generateAggregateQuery($aggregate, $criteria);
        return $this->querySQLValue($sql);
    }

    function beginTransaction() {
		$this->executeSQL("START TRANSACTION");
	}

    function callProcedure($name, ...$arguments) {
        $sql = $this->generator->generateProcedureCall($name, $arguments);
        return $this->querySQL($sql);
    }

	function commitTransaction() {
		$this->executeSQL("COMMIT");
	}

    function count(Criteria $criteria) {
		$result = $this->querySQL($this->generator->generateCount($criteria));
		return current(current($result));
	}

    function delete(Criteria $criteria) {
		return $this->executeSQL($this->generator->generateDelete($criteria));
	}

    function escapeIdentifier($name) {
        return $this->generator->escapeIdentifier($name);
    }

    function escapeValue($value) {
        return $this->generator->escapeValue($value);
    }

    function executeSQL($sql, array $parameters = null) {
        if ($parameters)
            $sql = $this->generator->interpolateParameters($sql, $parameters);

        if ($this->logger) {
            $this->logger->debug($sql);
            $startTime = microtime(true);
        }

        if ($this->connection->query($sql)) {
            if ($this->logger) {
                $elapsedTime = microtime(true) - $startTime;
                $this->logger->debug("{$this->connection->affected_rows} affected row(s), took {$elapsedTime}s");
            }
            return $this->affected_rows;
        } else {
            throw $this->translateException($this->connection->errno, $this->connection->error);
        }
    }

    function getCollection($name) {
		return new Collection($name);
	}

	function getConnection() {
		return $this->connection;
	}

	function getCriteria() {
		return new RelationalCriteria($this);
	}

    function getSQLBuffer() {
        return new SQLBuffer($this);
    }

    function query(Criteria $criteria) {
        return $this->querySQL($this->generator->generateSelect($criteria));
    }

    function querySQL($sql, array $parameters = null) {
        if ($parameters)
            $sql = $this->generator->interpolateParameters($sql, $parameters);

        echo "$sql\n";

        if ($this->logger) {
            $this->logger->debug($sql);
            $startTime = microtime(true);
        }

        $result = $this->connection->query($sql);

        if ($result) {
            $items = $this->processResult($result);
			echo "Returned ".count($items)." item(s)\n";

            if ($this->logger) {
                $elapsedTime = microtime(true) - $startTime;
                $count = count($items);
                $this->logger->debug("Query returned {$count} item(s)m took {$elapsedTime}s");
            }

            return $items;
        } else {
            throw $this->translateException($this->connection->errno, $this->connection->error);
        }
    }

    function rollbackTransaction() {
		$this->executeSQL("ROLLBACK");
	}

    function save(array $values, Collection $collection, $sequence = null) {
		$this->executeSQL($this->generator->generateInsert($values, $collection));
		return $this->connection->insert_id;
	}

    function saveMultiple(array $values, Collection $collection) {
		$this->executeSQL($this->generator->generateMultipleInsert($values, $collection));
        return $this->connection->insert_id;
	}

    function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
	}

    function update(array $values, Criteria $criteria) {
		return $this->executeSQL($this->generator->generateUpdate($values, $criteria));
	}

    protected function getResultTypes(mysqli_result $result) {
		$types = [];
		foreach ($result->fetch_fields() as $meta)
			$types[$meta->name] = $meta->type;
		return $types;
	}

    protected function processResult(mysqli_result $result) {
        $types = $this->getResultTypes($result);
        $items = [];

        while ($item = $result->fetch_assoc()) {
            $this->processResultItem($item, $types);
            $items[] = $item;
        }

        return $items;
    }

    protected function processResultItem(array &$items, array &$types) {
		foreach ($items as $field=>&$value) {
			if (!is_null($value)) {
				switch($types[$field]) {
					case self::TYPE_TINYINT:
					case self::TYPE_SMALLINT:
					case self::TYPE_MEDIUMINT:
					case self::TYPE_INT:
					case self::TYPE_BIGINT:
					case self::TYPE_YEAR:
						$value = (int) $value;
						continue;
					case self::TYPE_DOUBLE:
					case self::TYPE_FLOAT:
					case self::TYPE_DECIMAL:
						$value = (double) $value;
						continue;
					case self::TYPE_DATE:
					case self::TYPE_DATETIME:
					case self::TYPE_TIMESTAMP:
						$value = new DateTime($value);
						continue;
					case self::TYPE_TIME:
						$value = new DateTime("1970-01-01 $value");
						continue;
				}
			}
		}
	}

    protected function translateException($code, $message) {
		if ($this->logger)
			$this->logger->error($message);

        //echo "\nCODE $code\n";

		switch($code) {
			case 1044:
			case 1045:
				return new AuthException($message, $code);
			case 1049:
				return new UnknownSchemaException($message, $code);
            case 1054:
                return new UnknownFieldException($message, $code);
			case 1062:
				return new DuplicateEntryException($message, $code);
			case 1064:
				return new QuerySyntaxException($message, $code);
			case 1452:
				return new ConstraintException($message, $code);
			case 2002:
				return new UnknownHostException($message, $code);
			default:
				return new DataException($message, $code);
		}
    }

}
