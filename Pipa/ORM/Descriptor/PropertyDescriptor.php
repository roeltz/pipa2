<?php

namespace Pipa\ORM\Descriptor;

class PropertyDescriptor {

    public $cascaded = false;

    public $computedBy;

    public $eager = false;

    public $generated = false;

    public $name;

    public $notNull = false;

    public $orderByDefault;

    public $persistent = true;

    public $pk;

    public $underlyingName;

    public $transformations = [];

    function __construct($name) {
        $this->name = $name;
		$this->underlyingName = [$name];
    }

}
