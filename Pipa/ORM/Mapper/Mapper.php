<?php

namespace Pipa\ORM\Mapper;
use Pipa\ORM\Query\ORMCriteria;

interface Mapper {

    function map(ORMCriteria $source);

}
