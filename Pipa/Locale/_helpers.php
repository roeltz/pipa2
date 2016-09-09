<?php

use Pipa\Locale\Locale;
use Pipa\Util\String;

function __($message, $n = null, $values = null, $domain = null) {

    if (!is_integer($n)) {
        $domain = $values;
        $values = $n;
        $n = null;
    }

    if (!is_array($values)) {
        $domain = $values;
        $values = null;
    }

    if ($domain === null)
        $domain = "default";

    if ($locale = Locale::get()) {
        $translation = $locale->translate($message, $domain);
        if ($n !== null) {
            $translation = String::plural($translation, $n);
            $values["n"] = $n;
        }
        if ($values)
            $translation = String::fill($translation, $values);
        return $translation;
    } else {
        return $message;
    }
}

function __e(...$args) {
    echo __(...$args);
}
