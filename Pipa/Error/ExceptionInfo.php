<?php

namespace Pipa\Error;
use Exception;

class ExceptionInfo extends ErrorInfo {

	function __construct(Exception $e) {
		parent::__construct(
			get_class($e).': '.$e->getMessage(),
			$e->getCode(),
			$e->getFile(),
			$e->getLine(),
			$e->getTrace()
		);
	}
	
}
