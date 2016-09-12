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

	protected $engines = [];

	function checkEngineCondition(Request $request, array $conditions) {
		if ($conditions) {
			if ($accept = @$conditions["accept"]) {
				return preg_match("#{$accept}#i", @$request->headers["accept"]);
			} elseif ($ext = @$conditions["ext"]) {
				return preg_match("#\\.{$ext}\$#", $request->path);
			}
		} else {
			return true;
		}
	}

	function engine($name, $engine, array $conditions = []) {
		$this->engines[$name] = [$engine, $conditions];
		return $this;
	}

	function outputHeaders(Response $response) {
		header("{$response->context->protocol} {$response->status}");

		foreach($response->headers as $header=>$value)
			header("$header: $value");
	}

	function render(MVCRequest $request, MVCResponse $response, Result $result) {
		if ($response->body) {
			$body = $response->body;
		} else if ($engine = $this->selectEngine($request, $result)) {
			if (is_string($engine))
				$engine = new $engine();

			$this->emit("engine", $engine);
			$body = $engine->renderResponse($response, $result);
		} else {
			$body = "";
		}

		$this->outputHeaders($response);
		echo $body;
	}

	function selectEngine(Request $request, Result $result) {
		if ($engine = @$result->options["view-engine"]) {
			return $this->engines[$engine][0];
		} else {
			foreach ($this->engines as $name=>list($engine, $conditions)) {
				if ($this->checkEngineCondition($request, $conditions)) {
					return $engine;
				}
			}
		}
	}

}
