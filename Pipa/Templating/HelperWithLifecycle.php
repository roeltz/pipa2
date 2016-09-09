<?php

namespace Pipa\Templating;

interface HelperWithLifecycle {

	function startHelperLifecycle();

	function endHelperLifecycle();
	
}
