<?php

namespace Pipa\Locale;
use Exception;

class Locale {

    /**
     * @var string[] Locale codes expected to be used by the app
     */
    protected static $accepted = [];

    protected static $current;

    protected static $resourceClasses = [];

    protected static $resources = [];

    protected $code;

    static function accept(...$args) {
        self::$accepted = array_merge(self::$accepted, $args);
    }

    static function accepted() {
        return self::$accepted;
    }

    static function set(Locale $locale) {
        self::$current = $locale;
    }

    static function get() {
        if (!self::$current && self::$accepted)
            self::$current = new Locale(self::$accepted[0]);
        return self::$current;
    }

    static function registerResourceClass($class, $validator) {
        self::$resourceClasses[$class] = $validator;
    }

    static function registerResource($resource, $domain = "default") {
        if (!($resource instanceof Resource))
            $resource = self::getResource($resource);
        self::$resources[$domain][] = $resource;
    }

    static function getResource($filename) {
        foreach (self::$resourceClasses as $class=>$validator) {
            $validates = (is_callable($validator) && $validator($filename))
                        || (is_string($validator) && self::validateExtension($filename, $validator));
            if ($validates)
                return new $class($filename);
        }
        throw new Exception("Could not find suitable resource class for '$filename'");
    }

    static function validateExtension($filename, $extension){
    	return preg_match("/{$extension}\$/", $filename);
    }

    function __construct($code) {
        $this->code = $code;
    }

    function setEnvironment() {
        setlocale(LC_ALL, $this->code);
        self::set($this);
    }

    function translate($message, $domain = "default") {
        if (isset(self::$resources[$domain]))
            foreach (self::$resources[$domain] as $resource)
                if ($translation = $resource->getMessage($message, $this->code))
                    return $translation;
        return $message;
    }

}
