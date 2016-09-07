<?php

namespace Pipa\Registry;

class Registry {

    protected static $data = [];

    static function get($ns, $key, ...$args) {
        return self::getEntry($ns, $key)->getValue(...$args);
    }

    static function getEntry($ns, $key, $flags = 0, $type = null) {
        if (!isset(self::$data[$ns][$key]))
            self::$data[$ns][$key] = new Entry(null, $flags, $type);
        return self::$data[$ns][$key];
    }

    static function set($ns, $key, $value, $flags = 0, $type = null) {
        return self::getEntry($ns, $key, $flags, $type)->setValue($value);
    }

    static function setConstructor($ns, $key, callable $constructor, $type = null) {
        return self::getEntry($ns, $key, Entry::CONSTRUCTOR, $type)->setValue($constructor);
    }

    static function setSingleton($ns, $key, callable $constructor, $type = null) {
        return self::getEntry($ns, $key, Entry::SINGLETON, $type)->setValue($constructor);
    }

}
