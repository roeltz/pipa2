<?php

namespace Pipa\ORM\Descriptor;
use Pipa\Data\Query\Criteria;
use Pipa\ORM\Entity;
use Pipa\ORM\ORM;

class RelationDescriptor {

    public $property;

    public $class;

    public $fk = [];

    function __construct($property, $class, array $fk) {
        $this->property = $property;
        $this->class = $class;
        $this->fk = $fk ? $fk : [$property];
    }

	function applyKeys(Entity $entity, ClassDescriptor $descriptor, Criteria $criteria) {
		$pk = $descriptor->getPrimaryKeys();
		foreach ($this->fk as $i=>$key) {
			$value = $entity->getEntityRecordValue($key);
			$criteria->eq($pk[$i], $value);
		}
	}

    function isCompound() {
        return count($this->fk) > 1;
    }

    function isPersistent() {
        return true;
    }

}
