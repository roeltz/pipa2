<?php

namespace Pipa\Match;

class Matcher {
	
	protected $patterns;
	
	public $lastMatch;
	
	function __construct(array $patterns) {
		$this->patterns = $patterns;
	}
	
	function match($state, array &$extractedData) {
		
		$this->lastMatch = null;
		
		if ($state instanceof Comparable) {
			$state = $state->getComparableState();
		} elseif (is_object($state)) {
			$state = get_object_vars($state);
		}
		
		foreach ($this->patterns as $pattern) {
			if ($pattern->matches($state, $extractedData)) {
				$this->lastMatch = $pattern->value;
				return true;
			}
		}
		
		return false;
	}
}
