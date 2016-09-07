<?php

namespace Pipa\MVC;
use Pipa\Annotation\Reader;
use Pipa\Config\Config;

class ConfigOptionExtractor implements OptionExtractor {

    protected $map;

    function __construct(array $map = []) {
        $this->map = $map;
    }

    function getOptions(Action $action) {
        $options = [];

        foreach ($this->map as $option=>$key)
            $options[$option] = $action->context->config->get($key);

        return $options;
    }
}
