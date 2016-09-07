<?php

namespace Pipa\MVC;

interface ViewFilter {
	
	/**
	 * @param string buffer
	 * @return string
	 */
	function process($buffer);
	
}
