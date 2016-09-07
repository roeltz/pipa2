<?php

namespace Pipa\Cache;

class MemoryCache implements Cache {

	protected $cache = array();

	function destroy() {
		unset($this->cache);
		$this->cache = array();
		return $this;
	}

	function get($key) {
		return @$this->cache[$key];
	}

	function has($key) {
		return isset($this->cache[$key]);
	}

	function remove($key) {
		unset($this->cache[$key]);
		return $this;
	}

	function set($key, $value) {
		$this->cache[$key] = $value;
		return $this;
	}

}
