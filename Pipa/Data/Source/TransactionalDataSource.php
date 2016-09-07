<?php

namespace Pipa\Data\Source;

interface TransactionalDataSource {

    function beginTransaction();

	function commitTransaction();

	function rollbackTransaction();

}
