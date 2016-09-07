<?php

class Main {

	/** @HTML(view = "index", layout = "layout-basic") */
	function index() {
		return ["what"=>"mundo"];
	}
}
