<?php

namespace Pipa\HTTP;
use Pipa\MVC\Response as BaseResponse;
use Pipa\MVC\Result;

class Response extends BaseResponse {

	public $status = 200;

	public $headers = [];

	public $body;

	function setBody($body, $contentType = null) {
		if ($contentType)
			$this->setHeader("Content-Type", $contentType);

		$this->body = $body;
	}

	function setHeader($header, $value) {
		$this->headers[strtolower($header)] = $value;
	}
}
