<?php

namespace Pipa\Data\Query;
use Pipa\Data\Collection;
use Pipa\Data\Field;
use Pipa\Data\Query\ComparissionExpression;
use Pipa\Data\Query\Expression;
use Pipa\Data\Source\DataSource;

class Criteria extends ExpressionBuilder {

    public $collection;

    public $dataSource;

    public $distinct;

    public $fields = [];

    public $index;

    public $limit;

    public $offset;

    public $order = [];

    function __construct(DataSource $dataSource, Criteria $parent = null) {
		parent::__construct($parent);
        $this->dataSource = $dataSource;
    }

    function aggregate($operator, $field) {
        return $this->dataSource->aggregate(new Aggregate($operator, Field::from($field)), $this);
    }

    function count() {
        return $this->dataSource->count($this);
    }

    function delete() {
        return $this->dataSource->delete($this);
    }

    function distinct($distinct = true) {
		$this->distinct = $distinct;
        return $this;
    }

    function eq($field, $value = null) {
        if (is_array($field)) {
            foreach ($field as $f=>$value)
                $this->eq($f, $value);
        } else {
            $this->expressions[] = new ComparissionExpression(ComparissionExpression::EQ, Field::from($field), $value);
        }
        return $this;
    }

    function fields(...$fields) {
		if (is_array($fields[0]))
			$fields = $fields[0];

        foreach ($fields as $field)
            $this->fields[] = Field::from($field);

        return $this;
    }

    function from($collection) {
        $this->collection = Collection::from($collection, $this->dataSource);
        return $this;
    }

    function hasField($field) {
        $field = Field::from($field);
        foreach ($this->fields as $f)
            if ($f->name == $field->name && @$f->collection->name === @$field->collection->name)
                return true;
    }

    function indexBy($field) {
        $this->index = Field::from($field);
        return $this;
    }

    function limit($limit, $offset) {
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }

	function n($n) {
		return $this->limit($n, 0);
	}

    function orderBy($field, $type = Order::ASC) {
        $this->order[] = new Order(Field::from($field), $type);
        return $this;
    }

    function page($page, $size) {
		return $this->limit($size, ($page - 1) * $size);
	}

	function pageCount($size) {
		return ceil($this->count() / ($size > 0 ? $size : 1));
	}

    function queryAll() {
        if ($this->index && ($this->fields || !$this->hasField($this->index)))
            $this->fields[] = $this->index;

        $result = $this->dataSource->query($this);

        if ($this->index) {
            $indexed = [];
            foreach ($result as $item)
                $indexed[$item[$this->index->name]] = $item;
            return $indexed;
        }

        return $result;
    }

    function queryField($field) {
        $field = Field::from($field);
        $this->fields = [$field];

        if ($result = $this->queryAll())
            foreach ($result as &$item)
                $item = $item[$field->name];

        return $result;
    }

    function querySingle() {
		$this->n(1);
		if ($result = $this->queryAll())
			return current($result);
	}

	function queryValue() {
		if ($result = $this->querySingle())
			return current($result);
	}

    function update(array $values) {
        return $this->dataSource->update($values, $this);
    }

}
