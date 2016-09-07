<?php

namespace Pipa\Parse;

interface Rule {
	
	function matches($input, $n, &$l, Stack $stack);

}
