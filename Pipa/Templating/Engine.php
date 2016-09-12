<?php

namespace Pipa\Templating;
use Pipa\HTTP\Response;

interface Engine {

	function render($data, array $options);

}
