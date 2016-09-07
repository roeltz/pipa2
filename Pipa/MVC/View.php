<?php

namespace Pipa\MVC;

interface View {
	
	/**
	 * @return void
	 */
	function render(Request $request, Response $response, Result $result);
	
}
