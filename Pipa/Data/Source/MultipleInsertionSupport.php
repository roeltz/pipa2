<?php

namespace Pipa\Data\Source;
use Pipa\Data\Collection;

interface MultipleInsertionSupport {

	function saveMultiple(array $values, Collection $collection);

}
