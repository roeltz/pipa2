<?php

namespace Pipa\HTTP\View;
use Pipa\Templating\JSONEngine as BaseJSONEngine;

class JSONEngine extends BaseJSONEngine implements Engine {

    function renderResponse(Response $response, array $data, array $options) {
        $response->setHeader("Content-Type", "application/json");
        return $this->render($data, $options);
    }

}
