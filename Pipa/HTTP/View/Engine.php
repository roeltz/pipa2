<?php

namespace Pipa\HTTP\View;
use Pipa\HTTP\Response;
use Pipa\Templating\Engine as BaseEngine;

interface Engine extends BaseEngine {

    function renderResponse(Response $response, array $data, array $options);

}
