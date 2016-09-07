<?php

namespace Pipa\HTTP\Locale;
use Pipa\Locale\Extractor;
use Pipa\Locale\Locale;
use Pipa\MVC\Context;

class HeaderLocaleExtractor implements Extractor {

    function getLocale(Context $context) {
        if ($header = @$context->request->headers['accept-language']) {
			preg_match_all($this->getRegex(), $header, $matches);
			if ($matches)
				return new Locale($matches[0][0]);
		}
    }

    private function getRegex() {
		$regex = join("|", Locale::accepted());
		return "/$regex/i";
	}

}
