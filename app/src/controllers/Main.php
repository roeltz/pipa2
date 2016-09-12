<?php

class Main {

	/** @HTML("index") */
	function index() {
		return ["what"=>"mundo"];
	}

	/** @JSON */
	function test() {
		return new DateTime;
	}
}
