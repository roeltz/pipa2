<?php

use Pipa\Config\Config;
use Pipa\Error\ErrorHandler;
use Pipa\HTTP\Context;
use Pipa\HTTP\Error\HTMLErrorDisplay;
use Pipa\HTTP\Router;
use Pipa\HTTP\View;
use Pipa\HTTP\View\JSONEngine;
use Pipa\HTTP\View\PHPEngine;

include_once "vendor/autoload.php";

ErrorHandler::register();
ErrorHandler::addDisplay(new HTMLErrorDisplay());

$config = new Config();
$config
    ->load("app/config/app.json")
    ->load("app/config/http.json")
;

$router = new Router();
$router->load("app/config/routes/app.json");

$view = new View([
    "html"=>PHPEngine::class,
    "json"=>JSONEngine::class
]);

$context = new Context($config, $router, $view);
$context
    ->hook("app/src/hooks/error")
    ->hook("app/src/hooks/data")
    ->hook("app/src/hooks/locale")
	->hook("app/src/hooks/helpers")
    ->run()
;
