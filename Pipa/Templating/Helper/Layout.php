<?php

namespace Pipa\Templating\Helper;
use Pipa\Templating\Helper;
use Pipa\Templating\HelperWithLifecycle;

class Layout extends Helper implements HelperWithLifecycle {

	protected $contentBuffers = [];

	protected $stack = [];

	function begin($name) {
		ob_start();
		return $this;
	}

	function buffer($name) {
		$this->stack[0]["viewContent"] = $name;
		$this->begin($name);
		return $this;
	}

	function content($name, $content = null) {
		if ($content === null) {
			return @$this->contentBuffers[$name];
		} else {
			$this->contentBuffers[$name] = $content;
			return $this;
		}
	}

	function end($name) {
		$this->contentBuffers[$name] = ob_get_clean();
		return $this;
	}

	function endHelperLifecycle() {
		if ($this->stack) {
			$entry = array_shift($this->stack);
			$template = @$entry["template"];
			$viewContent = @$entry["viewContent"];

			if ($viewContent)
				$this->end($viewContent);

			if ($template)
				echo $this->renderWithCallingEngine([], ["view"=>$template]);
		}
	}

	function placeholder($name) {
		echo @$this->contentBuffers[$name];
		return $this;
	}

	function put($name, $view) {
		$this->contentBuffers[$name] = $this->renderWithCallingEngine([], ["view"=>$view]);
		return $this;
	}

	function template($name) {
		$this->stack[0]["template"] = $name;
		return $this;
	}

	function startHelperLifecycle() {
		array_unshift($this->stack, []);
	}

}
