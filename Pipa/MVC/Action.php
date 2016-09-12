<?php

namespace Pipa\MVC;
use DateTime;
use ReflectionMethod;
use ReflectionParameter;

class Action {

	const METHOD_PATTERN = '/^((?:\w+(?:\\\\\w+)*)?\w+)::(\w+)$/';

	public $context;

	protected static $optionExtractors = [];

	protected $callable;

	static function registerOptionExtractor(OptionExtractor $extractor) {
		self::$optionExtractors[] = $extractor;
	}

	function __construct(Context $context, $callable) {
		$this->context = $context;
		$this->callable = $callable;
	}

	function castArgument(ReflectionParameter $parameter, $value) {
		if ($class = $parameter->getClass()) {
			if ($class->getName() == "DateTime") {
				if (is_numeric($value)) {
					$date = new DateTime();
					$date->setTimestamp($value);
					return $date;
				} elseif (strlen($value)) {
					return new DateTime($value);
				} else {
					return null;
				}
			} elseif ($class->implementsInterface(__NAMESPACE__.'\Parameter')) {
				$instance = $class->newInstance();
				$instance->useParameterValue($value);
				return $instance;
			}
		} elseif (is_numeric($value)) {
			return (double) $value;
		}
		return $value;
	}

	/**
	 * @return Result
	 */
	function execute(Request $request, Response $response) {
		$method = $this->getReflector();
		$arguments = $this->getArguments($method, $request, $response);
		$controller = $this->getController($method);
		$options = $this->getOptions();
		return Result::from($method->invokeArgs($controller, $arguments), $options);
	}

	function getArguments(ReflectionMethod $function, Request $request, Response $response) {
		$arguments = array();
		foreach($function->getParameters() as $parameter) {
			$name = $parameter->getName();
			$class = $parameter->getClass();
			$value = @$request->data[$name];

			if (!is_null($value)) {
				$arguments[] = $this->castArgument($parameter, $value);
			} elseif ($parameter->isOptional()) {
				$arguments[] = $parameter->getDefaultValue();
			} elseif ($class) {
				if ($class->isInstance($request)) {
					$arguments[] = $request;
				} elseif ($class->isInstance($response)) {
					$arguments[] = $response;
				}
			} else {
				$arguments[] = null;
			}
		}
		return $arguments;
	}

	function getController(ReflectionMethod $method) {
		if ($method->isStatic()) {
			return null;
		} else {
			return $method->getDeclaringClass()->newInstance();
		}
	}

	function getReflector() {
		if (preg_match(self::METHOD_PATTERN, $this->callable, $m)) {
			return new ReflectionMethod($m[1], $m[2]);
		} else {
			throw new RoutingException("Invalid action");
		}
	}

	function getOptions() {
		$options = [];
		foreach (self::$optionExtractors as $extractor)
			$options = array_merge($options, $extractor->getOptions($this));
		return $options;
	}
}
