<?php

namespace Pipa\MVC;

interface User {
	
	/**
	 * @return string
	 */
	function getUserName();
	
	/**
	 * @return string[]
	 */
	function getUserRoles();
}
