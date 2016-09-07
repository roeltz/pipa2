<?php

namespace Pipa\ORM\Descriptor;
use Pipa\Data\Field;
use Pipa\Data\Query\ArrayExpressionParser;
use Pipa\Data\Query\Order;

class MultipleRelationDescriptor extends RelationDescriptor {

    public $order;

    public $constraints;

    public $path;

    function __construct($property, $class, array $fk, array $order, array $constraints, $path) {
        parent::__construct($property, $class, $fk);

        if ($order)
            foreach ($order as $field=>$type)
                $this->order[] = new Order(Field::from($field, $type));

        if ($constraints)
            $this->constraints = ArrayExpressionParser::parse($constraints);

        $this->path = $path;
    }

    function isPersistent() {
        return false;
    }

}
