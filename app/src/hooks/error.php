 <?php

use Pipa\MVC\Action;
use Pipa\MVC\RoutingException;

$pipeline->add("^error", function($next) use($context){
    try {
        $next();
    } catch(Exception $ex) {
		$context->reprocess("Errors::view", ["exception"=>$ex]);
    }
});
