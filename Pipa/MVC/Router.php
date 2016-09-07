<?php

namespace Pipa\MVC;

interface Router {

	/**
	 * @return Action
	 * @throws RoutingException
	 */
	function resolve(Request $request);
}
