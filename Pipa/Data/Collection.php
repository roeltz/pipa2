<?php

namespace Pipa\Data;
use Pipa\data\Source\DataSource;

class Collection {

    protected static $counter = 0;

    public $name;

    public $alias;

    static function from($collection, DataSource $dataSource = null) {
        if ($collection instanceof static) {
            return $collection;
        } else if ($dataSource) {
            return $dataSource->getCollection($collection);
        } else {
            return new static($collection);
        }
    }

    function __construct($name, $alias = null) {
        $this->name = $name;
        $this->alias = $alias ? $alias : "c".self::$counter++;
    }

    function field($name) {
        return new Field($name, $this);
    }
}
