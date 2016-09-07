<?php

namespace Pipa\Locale;
use Pipa\MVC\Context;

interface Extractor {

	function getLocale(Context $context);

}
