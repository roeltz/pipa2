<?php

namespace Pipa\Data\Relational;
use Pipa\Data\Collection;
use Pipa\Data\Field;
use Pipa\Data\Query\Criteria;

class RelationalCriteria extends Criteria {

    public $groups = [];

    public $joins = [];

    function join($collection, $a, $b, $type = Join::TYPE_INNER) {
        $this->joins[] = new Join(Collection::from($collection), Field::from($a), Field::from($b), $type);
        return $this;
    }

    function groupBy($field, array $aggregates) {
        $this->groups[] = new Group(Field::from($field), ...Group::parseAggregates($aggregates));
        return $this;
    }
}
