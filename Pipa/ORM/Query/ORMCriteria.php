<?php

namespace Pipa\ORM\Query;
use Pipa\Data\Query\Criteria;
use Pipa\Data\Source\DataSource;
use Pipa\ORM\Descriptor\ClassDescriptor;
use Pipa\ORM\Entity;
use Pipa\ORM\ORM;

class ORMCriteria extends Criteria {

    public $across = [];

    public $bring = [];

    public $invoke = [];

    public $descriptor;

    function __construct(DataSource $dataSource, ClassDescriptor $descriptor, ORMCriteria $parent = null) {
        parent::__construct($dataSource, $parent);
        $this->descriptor = $descriptor;
        $this->from($dataSource->getCollection($descriptor->collection));
		$this->fields($descriptor->getPersistedFields());
    }

    function across($path, ...$constraints) {
        $this->across[$path] = $constraints;
        return $this;
    }

    function bring($property, ...$constraints) {
        $bring = Bring::fromParentCriteria($this, $property);
        $this->bring[$property] = $bring;

        if (@$constraints[0] === true) {
            return $bring;
        } elseif ($constraints) {
            $bring->where($constraints);
            return $this;
        }
    }

    function getMappedCriteria() {
        $mapper = ORM::getMapper($this->descriptor->class);
        return $mapper->map($this);
    }

    function invoke($method, ...$arguments) {
        $this->invoke[] = new Invocation($method, $arguments);
		return $this;
    }

    function query() {
        $criteria = $this->getMappedCriteria();
        $result = $criteria->query();
        $result = $this->processResult($result);
        return $result;
    }

    function processItem(array $item, Entity $instance = null) {
        if (!$instance)
            $instance = new $this->descriptor->class;

        $instance->cast($item);

        foreach ($this->bring as $bring)
			$bring->apply($instance);

        foreach ($this->invoke as $invocation)
            $invocation->apply($instance);

        return $instance;
    }

    function processResult(array $result) {
        foreach ($result as &$item)
            $item = $this->processItem($item);
        return $result;
    }

}
