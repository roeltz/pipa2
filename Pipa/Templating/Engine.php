<?php

namespace Pipa\Templating;
use Pipa\HTTP\Response;

interface Engine {

	function render(array $data, array $options);

}
