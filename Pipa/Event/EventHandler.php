<?php

namespace Pipa\Event;

class EventHandler {
	
	public $callback;
	
	public $once;
	
	function __construct($callback, $once = false) {
		$this->callback = $callback;
		$this->once = $once;
	}
	
	function invoke(...$data) {
		$callback = $this->callback;
		return $callback(...$data);
	}
}
