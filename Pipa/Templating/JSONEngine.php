<?php

namespace Pipa\Templating;

class JSONEngine implements Engine {

    function render(array $data, array $options) {
        return json_encode($data);
    }

}
