<?php

namespace Pipa\HTTP\Option;
use Pipa\MVC\Option;

class HTML extends Option {

    public $name = Option::MULTIPLE;

    function filter(array $values) {
        $options = ["view-engine"=>"html"];

        if (isset($values["layout"])) {
            $options["view-layout"] = $values["layout"];
            $options["view"] = $values["view"];
        } else {
            $options["view"] = $values["value"];
        }

        return ["value"=>$options];
    }
}
