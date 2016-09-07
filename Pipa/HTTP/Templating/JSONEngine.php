<?php

namespace Pipa\HTTP\View;
use Pipa\HTTP\Response;

class JSONEngine implements Engine {

    function render(array $data, array $options, Response $response = null) {
        if ($response)
            $response->setHeader("Content-Type", "application/json");

        return json_encode($data);
    }
}
