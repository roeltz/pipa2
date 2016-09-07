<?php

namespace Pipa\Parse;

class Parser {
	
	protected $grammar;
	
	function __construct(Grammar $grammar) {
		$this->grammar = $grammar;
	}
		
	/**
	 * @return Annotation
	 * @throws SyntaxException
	 */
	function parse($input) {
		$l = 0;
		$length = strlen($input);
		$matches = [];
		$stack = new Stack();
		return $this->grammar->matches($input, 0, $l, $stack);
	}
}
