<?php

namespace Pipa\ORM\Annotation;
use Pipa\Annotation\Annotation;

class Many extends Annotation {

    public $class;

    public $fk;

    public $orderBy;

    public $where;

    public $path;

}
