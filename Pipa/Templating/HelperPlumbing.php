<?php

namespace Pipa\Templating;

abstract class HelperPlumbing {

    /**
	 * @var Helper[]
	 */
	static protected $globalHelpers = [];

    /**
	 * @var Helper[]
	 */
	protected $helpers = [];

	static function addGlobalHelper($name, $helper) {
		self::$globalHelpers[$name] = $helper;
	}

	function addHelper($name, $helper) {
		$this->helpers[$name] = $helper;
		return $this;
	}

	function getHelper($name) {
		return @$this->helpers[$name];
	}

	function initAllHelpers(array &$data, array &$options, Engine $callingEngine, $callingFile = null) {
		return array_merge(
			$this->initHelpers(self::$globalHelpers, $data, $options, $callingEngine, $callingFile),
			$this->initHelpers($this->helpers, $data, $options, $callingEngine, $callingFile)
		);
	}

    function initHelpers(array $helpers, array &$data, array &$options, Engine $callingEngine, $callingFile = null) {
        $objects = [];
        foreach ($helpers as $name=>$helper) {
            if (is_string($helper))
                $helper = new $helper();

			if ($helper instanceof Helper)
            	$helper->init($data, $options, $callingEngine, $callingFile);

            $objects[$name] = $helper;
        }
        return $objects;
    }
}
