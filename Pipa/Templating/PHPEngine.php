<?php

namespace Pipa\Templating;
use Exception;
use Pipa\HTTP\Response;

class PHPEngine extends HelperPlumbing implements Engine {

	function __construct() {
		$this->addHelper("html", Helper\HTML::class);
		$this->addHelper("layout", Helper\Layout::class);
		$this->addHelper("view", Helper\PHP::class);
	}

	function render(array $data, array $options) {
		$file = isset($options['view-layout'])
			? "{$options['view-dir']}/{$options['view-layout']}.php"
			: "{$options['view-dir']}/{$options['view']}.php"
		;

		extract($this->initAllHelpers($data, $options, $this, $file));
		extract($data);
		ob_start();

		$this->startHelpersLifecycles();

		try {
			require $file;
		} catch (Exception $ex) {
			ob_end_clean();
			throw $ex;
		}

		$this->endHelpersLifecycles();

		return ob_get_clean();
	}

}
