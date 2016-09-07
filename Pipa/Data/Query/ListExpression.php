<?php

namespace Pipa\Data\Query;
use Pipa\Data\Field;

class ListExpression implements Expression {

	const IN = "in";

	const NOT_IN = "not in";

	public $field;

	public $operator;

	public $values;

	function __construct($operator, Field $field, array $values = []) {
		$this->operator = $operator;
		$this->field = $field;
		$this->values = $values;
	}

	function __clone() {
		$this->field = clone $this->field;
	}
}
