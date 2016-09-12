<?php

namespace Pipa\HTTP\View;
use Pipa\HTTP\Response;
use Pipa\MVC\Result;
use Pipa\Templating\JSONEngine as BaseJSONEngine;

class JSONEngine extends BaseJSONEngine implements Engine {

    function renderResponse(Response $response, Result $result) {
        $response->setHeader("Content-Type", "application/json");
        return $this->render($result->data, $result->options);
    }

}
