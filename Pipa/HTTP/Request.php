<?php

namespace Pipa\HTTP;
use Pipa\MVC\Request as BaseRequest;
use Pipa\MVC\Session;

class Request extends BaseRequest {

	public $method;

	public $path;

	public $headers;

	public $host;

	public $https;

	function __construct(Context $context, Session $session, array $data, $method, $path, array $headers, $host, $https) {
		parent::__construct($context, $session, $data);
		$this->method = $method;
		$this->path = $path;
		$this->headers = $headers;
		$this->host = $host;
		$this->https = $https;
	}

	function getComparableState() {
		$state = parent::getComparableState();

		$state["method"] = $this->method;
		$state["path"] = $this->path;
		$state["host"] = $this->host;
		$state["https"] = $this->https;

		foreach ($this->headers as $k=>$v)
			$state["header:$k"] = $v;

		return $state;
	}

	function getURI() {
		return ($this->https ? "https" : "http") . "://{$this->host}/{$this->path}";
	}
}
