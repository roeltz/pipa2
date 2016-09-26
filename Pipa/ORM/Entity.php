<?php

namespace Pipa\ORM;
use Pipa\ORM\Query\Bring;

class Entity {

    protected $record;

    static function getCriteria() {
        return ORM::getCriteria(static::class);
    }

    function __construct(...$pk) {

    }

    function cast(array $record) {
        ORM::cast($this, $record);
        $this->record = $record;
    }

    function delete() {

    }

	function getEntityRecord() {
		return $this->record;
	}

	function getEntityRecordValue($key) {
		return $this->record[$key];
	}

    function save() {

    }

    function update() {

    }

	function bring($property, $returnCriteria = false) {
		$criteria = Bring::fromEntityInstance($this, $property);
		if ($returnCriteria) {
			return $criteria;
		} else {
			return $criteria->queryAll();
		}
	}

}
