<?php

namespace Pipa\Data\Source;

interface StoredProcedureSupport {

	function callProcedure($name, ...$arguments);

}
