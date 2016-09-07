<?php

use Pipa\MVC\Action;

$pipeline->add("^error", function($next) use($context){
    try {
        $next();
    } catch(Exception $ex) {
        $context->reprocess("Errors::{$context->action->getOptions()['view-engine']}", ["exception"=>$ex]);
    }
});
