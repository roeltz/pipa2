<?php

namespace Pipa\Data\Source;
use Pipa\Data\Collection;
use Pipa\Data\Query\Aggregate;
use Pipa\Data\Query\Criteria;

interface DataSource {

    function escapeValue($value);
    function getConnection();

    function getCollection($name);
    function getCriteria();

    function query(Criteria $criteria);
    function count(Criteria $criteria);
    function aggregate(Aggregate $aggregate, Criteria $criteria);

    function save(array $values, Collection $collection, $sequence = null);
    function update(array $values, Criteria $criteria);
    function delete(Criteria $criteria);
}
