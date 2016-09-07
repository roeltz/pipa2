<?php

namespace Pipa\Locale;
use Pipa\MVC\Context;

class L10n implements Extractor {

    protected $extractors = [];

    static function attach(Context $context) {
        $l10n = new L10n();
        $context->l10n = $l10n;
        $context->getPipeline()->addFirst("locale", function($next) use($l10n, $context){
            $context->locale = $l10n->getLocale($context);
            $next();
        });
        return $l10n;
    }

    function accept(...$codes) {
        Locale::accept(...$codes);
        return $this;
    }

    function extractor(Extractor $extractor) {
        $this->extractors[] = $extractor;
        return $this;
    }

    function resource($filename, $domain = "default") {
        Locale::registerResource($filename, $domain);
        return $this;
    }

    function getLocale(Context $context) {
        foreach ($this->extractors as $extractor) {
            if ($locale = $extractor->getLocale($context)) {
                return $locale;
            }
        }
    }
}
