<?php

class Main {

	/** @HTML("index") */
	function index() {
		return ["what"=>"mundo"];
	}

	/** @JSON */
	function test($arg) {
		return $arg;
	}
}
