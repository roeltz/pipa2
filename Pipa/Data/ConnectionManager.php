<?php

namespace Pipa\Data;
use Pipa\Data\Source\DataSource;
use Pipa\Registry\Registry;

abstract class ConnectionManager {

    static function get($name = "default") {
        return Registry::get(self::class, $name);
    }

    static function set($name, callable $callable) {
        Registry::setSingleton(self::class, $name, $callable, DataSource::class);
    }
}
