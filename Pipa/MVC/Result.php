<?php

namespace Pipa\MVC;

class Result {
		
	public $data;
	
	public $options;
	
	static function from($value, array $options = []) {
		if ($value instanceof Result) {
			if ($options)
				$value->options = array_merge($options, $value->options);
			return $value;
		} else {
			return new Result((array) $value, $options);
		}
	}
	
	function __construct(array $data, array $options = []) {
		$this->data = $data;
		$this->options = $options;
	}
}
