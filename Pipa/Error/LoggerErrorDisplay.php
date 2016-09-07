<?php

namespace Pipa\Error;
use Psr\Log\LoggerInterface;

class LoggerErrorDisplay implements ErrorDisplay {

	protected $logger;

	function __construct(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	function display(ErrorInfo $info) {
		$this->logger->error("{$info->message} ({$info->file}:{$info->line})", $info->stack);
	}
	
}
