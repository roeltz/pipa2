<?php

namespace Pipa\HTTP\View;
use Pipa\HTTP\Response;
use Pipa\Templating\PHPEngine as BasePHPEngine;

class PHPEngine extends BasePHPEngine implements Engine {

    function renderResponse(Response $response, array $data, array $options) {
        $response->setHeader("Content-Type", "text/html");
        return $this->render($data, $options);
    }

}
