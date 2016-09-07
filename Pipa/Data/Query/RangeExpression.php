<?php

namespace Pipa\Data\Query;
use Pipa\Data\Field;

class RangeExpression implements Expression {

	public $field;

	public $min;

	public $max;

	function __construct(Field $field, $min, $max) {
		$this->field = $field;
		$this->min = $min;
		$this->max = $max;
	}

	function __clone() {
		$this->field = clone $this->field;
	}
}
