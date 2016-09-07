<?php

namespace Pipa\ORM;
use Pipa\Cache\MemoryCache;
use Pipa\Data\Query\Criteria;

class InstanceCache extends MemoryCache {

	function hash(Criteria $criteria) {
		return md5(serialize($criteria->expressions));
	}

	function getFromCriteria(Criteria $criteria) {
		return $this->get($this->hash($criteria));
	}

	function setForCriteria(Criteria $criteria, Entity $value) {
		return $this->set($this->hash($criteria), $value);
	}

}
