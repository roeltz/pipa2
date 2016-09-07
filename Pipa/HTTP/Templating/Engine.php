<?php

namespace Pipa\HTTP\Templating;
use Pipa\HTTP\Response;
use Pipa\Templating\Engine as BaseEngine;

interface Engine extends Engine {

    function renderResponse(Response $response, array $data, array $options);

}
