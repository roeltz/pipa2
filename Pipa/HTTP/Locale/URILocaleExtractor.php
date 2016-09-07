<?php

namespace Pipa\HTTP\Locale;
use Pipa\Locale\Extractor;
use Pipa\Locale\Locale;
use Pipa\MVC\Context;

class URILocaleExtractor implements Extractor {

    const MODE_SUBDOMAIN = "subdomain";
	const MODE_PATH = "path";

	protected $mode;

    function __construct($mode) {
		$this->mode = $mode;
	}

    function getLocale(Context $context) {
        preg_match_all($this->getRegex(), $context->request->getURI(), $matches);
		if (@$matches[1][0])
			return new Locale($matches[1][0]);
    }

    private function getRegex() {
		$regex = join("|", Locale::accepted());
		switch($this->mode) {
			case self::MODE_SUBDOMAIN:
				$regex = "//($regex)\\.";
				break;
			case self::MODE_PATH:
				$regex = "/($regex)/";
				break;
		}
		return "#$regex#";
	}

}
