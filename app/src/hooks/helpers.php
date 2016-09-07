<?php

use Pipa\Templating\HelperPlumbing;

$context->view->on("engine", function($engine) use($context){
	if ($engine instanceof HelperPlumbing) {
		$engine->addHelper("context", $context);
		$engine->addHelper("request", $context->request);
		$engine->addHelper("response", $context->response);
		$engine->addHelper("session", $context->request->session);
		$engine->addHelper("user", $context->request->session->getUser());
	}
});
