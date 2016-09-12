<?php

namespace Pipa\HTTP\View;
use Pipa\HTTP\Response;
use Pipa\MVC\Result;
use Pipa\Templating\PHPEngine as BasePHPEngine;

class PHPEngine extends BasePHPEngine implements Engine {

    function renderResponse(Response $response, Result $result) {
        $response->setHeader("Content-Type", "text/html");
        return $this->render($result->data, $result->options);
    }

}
