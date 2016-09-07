<?php

namespace Pipa\Locale;

class PHPResource extends Resource {

	protected $data;

	function load($filename) {
		return require $filename;
	}
}
