<?php

class Errors {

    /** @HTML("error") */
    function html($exception) {
        return compact("exception");
    }

    /** @JSON */
    function json($exception) {
        return [
            "class"=>get_class($exception),
            "message"=>$exception->getMessage(),
            "code"=>$exception->getCode()
        ];
    }
}
