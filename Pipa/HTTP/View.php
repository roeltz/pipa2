<?php

namespace Pipa\HTTP;
use Pipa\Event\EventEmitterTrait;
use Pipa\HTTP\View\Engine;
use Pipa\MVC\Request as MVCRequest;
use Pipa\MVC\Response as MVCResponse;
use Pipa\MVC\Result;
use Pipa\MVC\View as ViewInterface;
use Pipa\MVC\ViewException;

class View implements ViewInterface {

	use EventEmitterTrait;

	protected $engines;

	function __construct(array $engines = []) {
		$this->engines = $engines;
	}

	function outputHeaders(Response $response) {
		header("{$response->context->protocol} {$response->status}");

		foreach($response->headers as $header=>$value)
			header("$header: $value");
	}

	function registerEngine($name, Engine $engine) {
		$this->engines[$name] = $engine;
	}

	function render(MVCRequest $request, MVCResponse $response, Result $result) {
		$engine = isset($result->options["view-engine"]) ? $result->options["view-engine"] : $request->context->config->get("http.view.default-engine");

		if (!$engine)
			throw new ViewException("A view engine was not set for this request");

		if ($response->body) {
			$body = $response->body;
		} else if (isset($this->engines[$engine])) {
			$engine = $this->engines[$engine];

			if (is_string($engine))
				$engine = new $engine();

			$this->emit("engine", $engine);
			
			$body = $engine->renderResponse($response, $result->data, $result->options);
		} else {
			$body = "";
		}

		$this->outputHeaders($response);
		echo $body;
	}
}
