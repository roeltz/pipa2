<?php

namespace Pipa\Event;
use ReflectionFunction;

trait EventEmitterTrait {
	
	protected $events = [];
	
	function emit($event, ...$data) {
		if (isset($this->events[$event])) {
			foreach($this->events[$event] as $i=>$handler) {
				$handler->invoke(...$data);
				if ($handler->once)
					unset($this->events[$event][$i]);
			}
		}
	}
	
	function on($event, $callback) {
		$this->register($event, $callback);
	}
	
	function once($event, $callback) {
		$this->register($event, $callback, true);
	}
	
	function off($event, $callback = null) {
		$this->unregister($event, $callback);
	}
	
	protected function register($event, $callback, $once = false) {
		$this->events[$event][] = new EventHandler($callback, $once);
	}
	
	protected function unregister($event, $callback = null) {
		if (isset($this->events[$event])) {
			if ($callback) {
				foreach($this->events[$event] as $i=>$handler) {
					if ($handler->handler === $callback) {
						unset($this->events[$event][$i]);
						break;
					}
				}
			} else {
				unset($this->events[$event]);
			}
		}
	}
}
