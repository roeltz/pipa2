<?php

namespace Pipa\HTTP\View;
use Pipa\HTTP\Response;
use Pipa\MVC\Result;

interface Engine {

    function renderResponse(Response $response, Result $result);

}
