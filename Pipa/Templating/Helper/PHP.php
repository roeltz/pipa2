<?php

namespace Pipa\Templating\Helper;
use Pipa\Templating\Helper;

class PHP extends Helper {

	function inline($view) {
		$this->put($view, $this->data, $this->options);
	}

    function put($view, array $data = [], array $options = []) {
        $engine = clone $this->callingEngine;
        return $engine->render($data, array_merge($options, [
            'view'=>$view,
            'view-dir'=>dirname($this->callingFile)
        ]));
    }

    function view() {
        $engine = clone $this->callingEngine;
        return $engine->render($this->data, [
            "view"=>$this->options["view"],
            "view-dir"=>$this->options["view-dir"]
        ]);
    }

    function __invoke($name = null, $data = null, $options = null) {
        if ($name) {
            return $this->put($name, is_array($data) ? $data : $this->data, is_array($options) ? $options : $this->options);
        } else {
            return $this->view();
        }
    }
}
