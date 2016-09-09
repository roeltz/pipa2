<?php

namespace Pipa\Templating\Helper;
use Pipa\Templating\Helper;

class Layout extends Helper {

	protected $contentBuffers = [];

	function begin($name) {
		ob_start();
		return $this;
	}

	function end($name) {
		$this->contentBuffers[$name] = ob_get_clean();
		return $this;
	}

	function content($name, $file) {
		return $this;
	}

	function placeholder($name) {
		return $this;
	}

	function template($name) {
		declare(ticks = 1);
		register_tick_function([$this, "check"]);
		return $this;
	}

	function check() {
		echo "CHECK<br>\n";
		print_r(debug_backtrace()[0]["file"].":".debug_backtrace()[0]["line"]."<br>\n");
	}

}
