<?php

namespace Pipa\Locale;
use Exception;
use Pipa\Util\String;

abstract class Resource {

    protected $data = [];

    protected $path;

    private $loaded = [];

    abstract function load($filename);

	final function __construct($path) {
		$this->path = $path;
	}

	function getMessage($message, $localeCode) {
        try {
            if (!@$this->data[$localeCode])
                $this->data[$localeCode] = $this->load($this->getLocalizedPath($localeCode));
            return $this->lookup($message, $localeCode);
        } catch(Exception $ex) {
            return $message;
        }
    }

    function lookup($message, $localeCode) {
        if (isset($this->data[$localeCode][$message])) {
            return $this->data[$localeCode][$message];
        } else {
            return $message;
        }
    }

	protected function getLocalizedPath($localeCode) {
		return String::interpolate($this->path, function($k) use($localeCode) {
            return $k == "locale" ? $localeCode : $k;
        });
	}

}
