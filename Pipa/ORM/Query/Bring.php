<?php

namespace Pipa\ORM\Query;
use Pipa\ORM\Entity;
use Pipa\ORM\ORM;
use Pipa\ORM\Descriptor\ClassDescriptor;
use Pipa\ORM\Descriptor\MultipleRelationDescriptor;

class Bring extends ORMCriteria {

    public $property;

	private $mapped;

	static function fromParentCriteria(ORMCriteria $parent, $property) {
		return new Bring($parent->descriptor, $property, $parent);
	}

	static function fromEntityInstance(Entity $instance, $property) {
		return new Bring(ORM::getDescriptor($instance), $property);
	}

    function __construct(ClassDescriptor $sourceDescriptor, $property, ORMCriteria $parent = null) {
		$descriptor = $sourceDescriptor->getRelatedClassDescriptor($property);
        parent::__construct(ORM::getDataSource($descriptor->class), $descriptor, $parent);
		$this->property = $property;
    }

	function apply(Entity $entity) {
		if (!$this->mapped)
			$this->mapped = $this->getMappedCriteria();

		$copy = clone $this->mapped;

		$relationDescriptor = ORM::getDescriptor($entity)->getRelationDescriptor($this->property);
		$relationDescriptor->applyKeys($entity, $this->descriptor, $copy);

		if ($relationDescriptor instanceof MultipleRelationDescriptor) {
			$result = $copy->query();
			$result = $this->processResult($result);
		} else {
			$cache = ORM::getInstanceCache($this->descriptor->class);
			if ($cachedInstance = $cache->getFromCriteria($copy)) {
				$result = $cachedInstance;
			} else {
				$result = $copy->querySingle();
				if ($result)
					$result = $this->processItem($result);
				$cache->setForCriteria($copy, $result);
			}
		}

		$entity->{$this->property} = $result;
	}

}
