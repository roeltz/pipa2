<?php

namespace Pipa\Cache;
use Memcache;

class MemcachedCache implements Cache {

	private $memcache;

	function __construct($host, $port) {
		$this->memcache = new Memcache;
		$this->memcache->pconnect($host, $port);
	}

	function destroy() {
		
	}

	function get($key) {
		return $this->memcache->get($key);
	}

	function has($key) {
		return $this->memcache->get($key) !== null;
	}

	function remove($key) {
		return $this->memcache->delete($key);
	}

	function set($key, $value, $expire = 0) {
		return $this->memcache->set($key, $value, 0, $expire);
	}

}
