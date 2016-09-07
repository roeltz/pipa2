<?php

namespace Pipa\Data\SQL;
use Pipa\Data\Collection;
use Pipa\Data\Field;
use Pipa\Data\Query\Aggregate;
use Pipa\Data\Query\Criteria;
use Pipa\Data\Query\ComparissionExpression;
use Pipa\Data\Query\Expression;
use Pipa\Data\Query\JunctionExpression;
use Pipa\Data\Query\ListExpression;
use Pipa\Data\Query\NegationExpression;
use Pipa\Data\Query\Order;
use Pipa\Data\Query\RangeExpression;
use Pipa\Data\Query\SQLExpression;
use Pipa\Data\Relational\Join;
use Pipa\Data\Relational\RelationalCriteria;
use Pipa\Util\String;

abstract class GenericSQLGenerator {

	abstract function escapeField(Field $field);
	abstract function escapeIdentifier($name);
	abstract function escapeValue($value);

	function generateAggregateQuery(Aggregate $aggregate, Criteria $criteria) {
		return join(' ', $this->generateAggregateQueryComponents($aggregate, $criteria));
	}

	function generateAggregateQueryComponents(Aggregate $aggregate, Criteria $criteria) {
		$components = $this->generateSelectComponents($criteria);
		$components['fields'] = strtoupper($aggregate->operation).'('.$this->escapeField($aggregate->field).')';
		return $components;
	}

	function generateAggregateComponents(Aggregate $aggregate) {
		$components = [
			'operation'=>strtoupper($aggregate->operation),
			'open'=>'(',
			'field'=>$aggregate->field->name == '*' ? '*' : $this->escapeField($aggregate->field),
			'close'=>')'
		];

		if ($aggregate->alias) {
			$components['alias-as'] = ' AS ';
			$components['alias-name'] = $this->escapeField($aggregate->alias);
		}

		return $components;
	}

	function generateCount(Criteria $criteria) {
		return join(' ', $this->generateCountComponents($criteria));
	}

	function generateCountComponents(Criteria $criteria) {
		$components = $this->generateSelectComponents($criteria);
		$components['fields'] = 'COUNT(*)';
		return $components;
	}

	function generateDelete(Criteria $criteria) {
		return join(' ', $this->generateDeleteComponents($criteria));
	}

	function generateDeleteComponents(Criteria $criteria) {
		$components = ['keyword'=>'DELETE FROM'];
		$components['collection'] = $this->renderCollection($criteria->collection);
		if ($criteria->expressions)
			$components['where'] = 'WHERE '.$this->renderExpressions($criteria->expressions);
		return $components;
	}

	function generateInsert(array $values, Collection $collection) {
		return join(' ', $this->generateInsertComponents($values, $collection));
	}

	function generateMultipleInsert(array $values, Collection $collection) {
		return join(' ', $this->generateMultipleInsertComponents($values, $collection));
	}

	function generateInsertComponents(array $values, Collection $collection) {
		$components = $this->generateInsertHeaderComponents(array_keys($values), $collection);
		$components['values'] = 'VALUES '.$this->generateInsertValues($values);
		return $components;
	}

	function generateMultipleInsertComponents(array $values, Collection $collection) {
		$components = $this->generateInsertHeaderComponents(array_keys($values[0]), $collection);
		$valuesList = [];
		foreach ($values as $row)
			$valuesList[] = $this->generateInsertValues($row);
		$components['values'] = 'VALUES '.join(', ', $valuesList);
		return $components;
	}

	function generateInsertHeaderComponents(array $fields, Collection $collection) {
		$components = ['keyword'=>'INSERT INTO'];
		$components['collection'] = $this->renderCollection($collection);
		$escapedFields = [];

		foreach ($fields as $field)
			$escapedFields[] = $this->escapeField(Field::from($field));
		$components['fields'] = '('.join(', ', $escapedFields).')';
		return $components;
	}

	function generateInsertValues(array $values) {
		$escapedValues = [];
		foreach ($values as $value) {
			$escapedValues[] = $this->escapeValue($value);
		}
		return '('.join(', ', $escapedValues).')';
	}

	function generateSelect(Criteria $criteria, array $fields = null) {
		return join(" ", array_filter($this->generateSelectComponents($criteria, $fields)));
	}

	function generateSelectComponents(Criteria $criteria, array $fields = null) {
		$components = ['keyword'=>'SELECT'];
		$fields = $this->generateFieldList($criteria);

		if ($fields) {
			if ($criteria->distinct)
				$components['distinct'] = 'DISTINCT';
			$components['fields'] = join(', ', $fields);
		} else {
			$components['fields'] = '*';
		}

		$components['collection'] = 'FROM '.$this->renderCollection($criteria->collection);

		if ($criteria->expressions && $where = $this->renderExpressions($criteria->expressions)) {
			$components['where'] = "WHERE $where";
		} else {
			$components['where'] = '';
		}

		if ($criteria->order)
			$components['order-by'] = $this->renderOrder($criteria->order);

		if ($criteria->limit)
			$components['limit'] = $this->renderLimit($criteria->limit, $criteria->offset);

		return $components;
	}

	function generateFieldList(Criteria $criteria) {
		$fields = [];

		if ($criteria->fields)
			$fields = array_map([$this, 'escapeField'], $criteria->fields);

		if ($criteria instanceof RelationalCriteria && $criteria->groups)
			foreach ($criteria->groups as $group)
				foreach ($group->aggregates as $aggregate)
					$fields[] = join('', $this->generateAggregateComponents($aggregate));

		return $fields;
	}

	function generateUpdate(array $values, Criteria $criteria) {
		return join(' ', $this->generateUpdateComponents($values, $criteria));
	}

	function generateUpdateComponents(array $values, Criteria $criteria) {
		$components = ['keyword'=>'UPDATE'];

		$components['collection'] = $this->renderCollection($criteria->collection);

		$vars = [];
		foreach ($values as $field=>$value) {
			$vars[] = $this->escapeField(Field::from($field)).' = '.$this->escapeValue($value);
		}
		$components['set'] = 'SET '.join(', ', $vars);

		if ($criteria->expressions)
			$components['where'] = 'WHERE '.$this->renderExpressions($criteria->expressions);

		return $components;
	}

	function interpolateParameters($sql, array $parameters) {
		return String::interpolate($sql, function($key) use($parameters){
			if ($key[0] == ":") {
				$key = substr($key, 1);
				$key = isset($parameters[$key]) ? $parameters[$key] : $key;
				return $this->escapeIdentifier($key);
			} else {
				return $this->escapeValue(@$parameters[$key]);
			}
		});
	}

	function renderCollection(Collection $collection) {
		$rendered = $this->escapeIdentifier($collection->name);

		if ($collection->alias)
			$rendered .= " AS ".$this->escapeIdentifier($collection->alias);

		return $rendered;
	}

	function renderComparissionExpression(ComparissionExpression $expression) {
		$a = $this->escapeField($expression->a);
		$o = $expression->operator;
		$b = $expression->b instanceof Field ? $this->escapeField($expression->b) : $this->escapeValue($expression->b);

		if ($o == '=' && $b === null) {
			return "$a IS NULL";
		} elseif ($o == '<>' && $b === null) {
			return "$a IS NOT NULL";
		} elseif ($o == 'like') {
			return $this->renderLike($a, $b);
		} elseif ($o == 'regex') {
			return $this->renderRegex($a, $b);
		} else {
			return "$a $o $b";
		}
	}

	function renderExpression(Expression $expression) {
		if ($expression instanceof ComparissionExpression) {
			return $this->renderComparissionExpression($expression);
		} elseif ($expression instanceof ListExpression) {
			return $this->renderListExpression($expression);
		} elseif ($expression instanceof RangeExpression) {
			return $this->renderRangeExpression($expression);
		} elseif ($expression instanceof JunctionExpression) {
			return $this->renderJunctionExpression($expression);
		} elseif ($expression instanceof NegationExpression) {
			return $this->renderNegationExpression($expression);
		} elseif ($expression instanceof SQLExpression) {
			return $this->renderSQLExpression($expression);
		}
	}

	function renderExpressions(array $expressions) {
		if (count($expressions) == 1 && $expressions[0] instanceof JunctionExpression) {
			$expressions = $expressions[0];
		} else {
			$conjunction = new JunctionExpression(JunctionExpression::CONJUNCTION);
			$expressions = $conjunction->addAll($expressions);
		}
		return $this->renderExpression($expressions);
	}

	function renderJunctionExpression(JunctionExpression $expression) {
		$rendered = [];
		foreach ($expression->expressions as $e) {
			if ($e = $this->renderExpression($e))
				$rendered[] = $e;
		}

		if ($rendered)
			return '('.join(strtoupper(") {$expression->operator} ("), $rendered).')';
	}

	function renderLike($a, $b) {
		return "$a LIKE $b";
	}

	function renderListExpression(ListExpression $expression) {
		if ($expression->values) {
			$values = [];
			foreach ($expression->values as $v)
				$values[] = $this->escapeValue($v);
			$values = join(', ', $values);
			if ($values) {
				$field = $this->escapeField($expression->field);
				switch ($expression->operator) {
					case ListExpression::IN:
						return "$field IN ($values)";
					case ListExpression::NOT_IN:
						return "$field NOT IN ($values)";
				}
			}
		}
	}

	function renderLimit($length, $offset) {
		if ($length == -1) {
			return "OFFSET {$offset}";
		} else {
			return "LIMIT {$length} OFFSET {$offset}";
		}
	}

	function renderNegationExpression(NegationExpression $expression) {
		return 'NOT ('.$this->renderExpression($expression).')';
	}

	function renderGroups(array $groups) {
		$rendered = [];
		foreach ($groups as $group)
			$rendered[] = $this->escapeField($group->field);

		return 'GROUP BY '.join(', ', $rendered);
	}

	function renderOrder(array $order) {
		$rendered = [];
		foreach ($order as $o) {
			switch ($o->type) {
				case Order::ASC:
					$rendered[] = $this->escapeField($o->field).' ASC';
					break;
				case Order::DESC:
					$rendered[] = $this->escapeField($o->field).' DESC';
					break;
				case Order::RANDOM:
					$rendered[] = $this->getRandomOrderExpression();
					break;
				default:
					continue;
			}
		}
		return 'ORDER BY '.join(', ', $rendered);
	}

	function renderRangeExpression(RangeExpression $expression) {
		$field = $this->escapeField($expression->field);
		$min = $this->escapeValue($expression->min);
		$max = $this->escapeValue($expression->max);
		return "$field BETWEEN $min AND $max";
	}

	function renderRegex($a, $b) {
		return "$a SIMILAR TO $b";
	}

	function renderSQLExpression(SQLExpression $expression) {
		if ($expression->parameters) {
			return String::fill($expression->sql, $expression->parameters);
		} else {
			return $expression->sql;
		}
	}

	function insertComponent(array &$components, $key, array $new) {
		$index = array_search($key, array_keys($components)) + 1;
		$components = array_merge(
			array_slice($components, 0, $index),
			$new,
			array_slice($components, $index)
		);
	}
}
