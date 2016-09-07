<?php

namespace Pipa\Pipeline;

use Exception;

class Pipeline {

	const PATTERN_FIRST = '/^\^(.+)/';
	const PATTERN_LAST = '/^([^$]+)\$$/';
	const PATTERN_BEFORE = '/^([^<]+)<(.+)$/';
	const PATTERN_AFTER = '/^([^>]+)>(.+)$/';
	const STEP_ERROR = "error";

	protected $steps = [];

	protected $index = [];

	function add($step, callable $callable) {
		if (preg_match(self::PATTERN_FIRST, $step, $match)) {
			$this->addFirst($match[1], $callable);
		} elseif (preg_match(self::PATTERN_LAST, $step, $match)) {
			$this->addLast($match[1], $callable);
		} elseif (preg_match(self::PATTERN_BEFORE, $step, $match)) {
			$this->addBefore($match[1], $match[2], $callable);
		} elseif (preg_match(self::PATTERN_AFTER, $step, $match)) {
			$this->addAfter($match[1], $match[2], $callable);
		} else {
			$this->addLast($step, $callable);
		}
		return $this;
	}

	function addFirst($step, callable $callable) {
		$this->set($step, $callable);
		array_unshift($this->index, $step);
		return $this;
	}

	function addLast($step, callable $callable) {
		$this->set($step, $callable);
		$this->index[] = $step;
		return $this;
	}

	function addBefore($step, $ref, callable $callable) {
		$this->set($step, $callable);
		array_splice($this->index, array_search($ref, $this->index), 0, $step);
		return $this;
	}

	function addAfter($step, $ref, callable $callable) {
		$this->set($step, $callable);
		array_splice($this->index, array_search($ref, $this->index) + 1, 0, $step);
		return $this;
	}

	function getStepFromExpression($expression) {
		foreach ([self::PATTERN_FIRST, self::PATTERN_LAST, self::PATTERN_BEFORE, self::PATTERN_AFTER] as $pattern)
			if (preg_match($pattern, $expression, $m))
				return $m[1];
		return $expression;
	}

	function set($step, callable $callable) {
		$this->steps[$step] = $callable;
		return $this;
	}

	function run($offset = null) {
		$steps = $offset ? array_slice($this->index, array_search($offset, $this->index)) : $this->index;
		return $this->runSteps($steps);
	}

	protected function runSteps(array $steps) {
		call_user_func($this->steps[$steps[0]], function() use($steps) {
			if (count($steps) > 1)
				$this->runSteps(array_slice($steps, 1));
		});
	}
}
