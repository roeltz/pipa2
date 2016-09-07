<?php

namespace Pipa\Data\SQL;
use Pipa\Data\Query\Criteria;

interface SQLDataSource {

    function escapeIdentifier($name);

    function executeSQL($sql, array $parameters = null);

    function getSQLBuffer();

    function querySQL($sql, array $parameters = null);

    function querySQLSingle($sql, array $parameters = null);

    function querySQLField($sql, array $parameters = null);

    function querySQLValue($sql, array $parameters = null);

}
