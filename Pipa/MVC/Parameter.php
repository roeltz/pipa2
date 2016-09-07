<?php

namespace Pipa\MVC;

interface Parameter {

    /**
     * @return Parameter
     * @throws \InvalidArgumentException
     */
    function newInstanceFromParameterValue($value);
}
