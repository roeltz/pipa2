<?php

namespace Pipa\HTTP;
use Pipa\MVC\Action;
use Pipa\MVC\AnnotationOptionExtractor;
use Pipa\MVC\ConfigOptionExtractor;
use Pipa\HTTP\View\PHPEngine;

Action::registerOptionExtractor(new ConfigOptionExtractor([
    "view-dir"=>"http.view.dir",
    "view-engine"=>"http.view.default-engine"
]));

Action::registerOptionExtractor(new AnnotationOptionExtractor(['Pipa\HTTP\Option']));
