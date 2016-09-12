<?php

class Errors {

    /** @View("error") */
    function view($exception) {
        return compact("exception");
    }

}
