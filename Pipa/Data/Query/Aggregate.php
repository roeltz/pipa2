<?php

namespace Pipa\Data\Query;
use Pipa\Data\Field;

class Aggregate {

	public $field;

	public $operation;

	public $alias;

	function __construct($operation, Field $field, $alias = null) {
		$this->operation = $operation;
		$this->field = $field;
		$this->alias = $alias;
	}
}
