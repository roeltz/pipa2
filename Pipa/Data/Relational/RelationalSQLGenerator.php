<?php

namespace Pipa\Data\Relational;
use Pipa\Data\Query\Criteria;
use Pipa\Data\SQL\GenericSQLGenerator;

abstract class RelationalSQLGenerator extends GenericSQLGenerator {

	function generateSelectComponents(Criteria $criteria, array $fields = null) {
		$components = parent::generateSelectComponents($criteria, $fields);

		if ($criteria instanceof RelationalCriteria) {
			if ($criteria->joins) {
				$this->insertComponent($components, "collection", [
					"join"=>$this->renderJoins($criteria->joins)
				]);
			}

			if ($criteria->groups) {
				$this->insertComponent($components, "where", [
					"group-by"=>$this->renderGroups($criteria->groups)
				]);
			}
		}

		return $components;
	}

	function renderJoins(array $joins) {
		return join(" ", array_map([$this, "renderJoin"], $joins));
	}

	function renderJoin(Join $join) {
		$type = strtoupper($join->type);
		$c = $this->renderCollection($join->collection);
		$a = $this->escapeField($join->a);
		$b = $this->escapeField($join->b);
		return "$type JOIN $c ON $a = $b";
	}
}
