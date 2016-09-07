<?php

namespace Pipa\Error;

class ErrorInfo {

	public $code;

	public $file;

	public $line;

	public $message;

	public $stack;

	function __construct($message, $code, $file, $line, array $stack) {
		$this->message = $message;
		$this->code = $code;
		$this->file = $file;
		$this->line = $line;
		$this->stack = $stack;
	}
	
}
