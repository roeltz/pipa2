<?php

namespace Pipa\Data;

class Field {

    public $name;

    public $collection;

    static function from($field, $collection = null) {
        if ($field instanceof static) {
            return $field;
        } else {
            return new static($field, $collection);
        }
    }

    function __construct($name, Collection $collection = null) {
        $this->name = $name;
        $this->collection = $collection;
    }
}
