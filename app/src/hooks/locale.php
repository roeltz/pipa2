<?php

use Pipa\HTTP\Locale\HeaderLocaleExtractor;
use Pipa\HTTP\Locale\URILocaleExtractor;
use Pipa\Locale\L10n;

L10n::attach($context)
    ->accept("es", "en")
    ->extractor(new URILocaleExtractor(URILocaleExtractor::MODE_PATH))
    ->extractor(new HeaderLocaleExtractor())
    //->resource("app/lang/{locale}.php")
    ->resource("app/lang/{locale}.mo")
;
