<?php

namespace Pipa\Config;

class Config {
	
	protected $config = [];
	
	function get($key, $default = null) {
		$value = $this->lookup($key);
		return $value ? $value : $default;
	}
	
	function set($key, $value) {
		$key = &$this->lookup($key);
		$key = $value;
		return $this;
	}
	
	function load($path) {
		$loadedConfig = json_decode(file_get_contents($path), JSON_OBJECT_AS_ARRAY);
		$this->config = array_merge_recursive($this->config, $loadedConfig);
		return $this;
	}
	
	protected function &lookup($key) {
		$currentLevel = &$this->config;
		$components = explode(".", $key);
		while ($components) {
			$property = array_shift($components);
			if (!isset($currentLevel[$property]))
				$currentLevel[$property] = [];
			$currentLevel = &$currentLevel[$property];
		}
		return $currentLevel;
	}
}
