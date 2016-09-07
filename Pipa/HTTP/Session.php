<?php

namespace Pipa\HTTP;
use Pipa\MVC\Session as BaseSession;

class Session extends BaseSession {

	protected $id;

	function __construct($id = null) {
		if ($id) {
			session_id($id);
			$this->id = $id;
		} else {
			if (!session_id())
				session_start();
			$this->id = session_id();
		}
	}

	function destroy() {
		session_destroy();
	}

	function get($key) {
		return @$_SESSION[$key];
	}

	function has($key) {
		return isset($_SESSION[$key]);
	}

	function remove($key) {
		unset($_SESSION[$key]);
	}

	function set($key, $value) {
		$_SESSION[$key] = $value;
	}

	function setLifetime($seconds) {
		session_set_cookie_params($seconds);
		session_id($this->id);
		session_start();
	}

}
