<?php

namespace Pipa\HTTP;
use Exception;
use Pipa\Match\Matcher;
use Pipa\Match\Pattern;
use Pipa\MVC\Action;
use Pipa\MVC\Request as MVCRequest;
use Pipa\MVC\Router as RouterInterface;
use Pipa\MVC\RoutingException;

class Router implements RouterInterface {

	const EXPRESSION_PATTERN = '#(?:(HTTPS)\s+)?(?:(GET|POST|PUT|DELETE)\s+)?(?://([^/])+/)?(\S+)#i';

	protected $patterns = [];

	function add($expression, $action) {
		$this->patterns[] = $this->parse($expression, $action);
		return $this;
	}

	function load($path) {
		$data = json_decode(file_get_contents($path));
		$base = isset($data->base) ? $data->base : "/";
		$basePattern = $this->parse($base);

		foreach($data->routes as $route=>$action) {
			$routePattern = $this->parse($route, $action);
			$actionPattern = $this->merge($basePattern, $routePattern);
			$this->patterns[] = $actionPattern;
		}
	}

	function merge(Pattern $a, Pattern $b) {
		$c = new Pattern($b->value);
		$c->state = array_merge($a->state, $b->state);
		$c->state["path"]["capture"] = $a->state["path"]["capture"] . $b->state["path"]["capture"];
		return $c;
	}

	function parse($expression, $action = null) {
		if (preg_match(self::EXPRESSION_PATTERN, $expression, $m)) {
			$pattern = new Pattern($action);
			$pattern
				->equals("method", $m[2] ? strtoupper($m[2]) : "GET")
				->capture("path", $m[4]);

			if (strtoupper($m[1]) == "HTTPS")
				$pattern->equals("https", true);

			if ($m[3])
				$pattern->capture("host", $m[3]);

			return $pattern;
		} else {
			throw new Exception("Invalid routing expression: $expression");
		}
	}

	function resolve(MVCRequest $request) {
		$matcher = new Matcher($this->patterns);
		$state = $request->getComparableState();
		$extractedData = [];

		if ($matcher->match($state, $extractedData)) {
			$request->data = array_merge($request->data, $extractedData);
			return new Action($request->context, $matcher->lastMatch);
		} else {
			throw new RoutingException("Action not found");
		}
	}
}
