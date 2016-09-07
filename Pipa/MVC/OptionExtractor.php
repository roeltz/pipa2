<?php

namespace Pipa\MVC;

interface OptionExtractor {

    /**
     * @return array
     */
    function getOptions(Action $action);
}
