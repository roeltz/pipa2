<?php

namespace Pipa\MVC;

class Response {
		
	public $context;
	
	function __construct(Context $context) {
		$this->context = $context;
	}

	function endBuffer() {
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}
		
	function render(Result $result) {
	}

	function startBuffer() {
		ob_start();
	}
}
