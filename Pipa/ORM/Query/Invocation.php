<?php

namespace Pipa\ORM\Query;
use Pipa\ORM\Entity;

class Invocation {

    public $method;

    public $args;

    function __construct($method, array $args = []) {
        $this->method = $method;
        $this->args = $args;
    }

    function apply(Entity $entity) {
        call_user_func_array([$entity, $this->method], $this->args);
    }
}
