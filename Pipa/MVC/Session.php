<?php

namespace Pipa\MVC;

abstract class Session {
	
	protected $userKey;

	/**
	 * @return void
	 */
	abstract function destroy();
	
	/**
	 * @param string key
	 * @return mixed
	 */
	abstract function get($key);
	
	/**
	 * @param string key
	 * @return bool
	 */
	abstract function has($key);

	/**
	 * @param string key
	 * @return void
	 */
	abstract function remove($key);
	
	/**
	 * @param string key
	 * @param mixed value
	 * @return void
	 */
	abstract function set($key, $value);
	
	/**
	 * @param int seconds
	 * @return void
	 */
	abstract function setLifetime($seconds);
	
	/**
	 * @return null|User
	 */
	function getUser() {
		return $this->get($this->userKey);
	}

	/**
	 * @return void
	 */
	function removeUser() {
		$this->remove($this->userKey);
	}

	/**
	 * @param User user
	 * @return void
	 */
	function setUser(User $user) {
		$this->set($this->userKey, $user);
	}
}
