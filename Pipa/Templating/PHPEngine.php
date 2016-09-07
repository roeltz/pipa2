<?php

namespace Pipa\Templating;
use Exception;
use Pipa\HTTP\Response;

class PHPEngine extends HelperPlumbing implements Engine {

	function __construct() {
		$this->addHelper("view", Helper\PHP::class);
		$this->addHelper("html", Helper\HTML::class);
	}

	function render(array $data, array $options) {
		$file = isset($options['view-layout'])
			? "{$options['view-dir']}/{$options['view-layout']}.php"
			: "{$options['view-dir']}/{$options['view']}.php"
		;

		ob_start();
		extract($this->initAllHelpers($data, $options, $this, $file));
		extract($data);

		try {
			require $file;
		} catch (Exception $ex) {
			ob_end_clean();
			throw $ex;
		}

		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}


}
