<?php

namespace Pipa\MVC;
use Pipa\Annotation\Reader;

class AnnotationOptionExtractor implements OptionExtractor {

    protected $namespaces;

    function __construct(array $namespaces = []) {
        $this->namespaces = $namespaces;
    }

    function getOptions(Action $action) {
        $method = $action->getReflector();
        $options = [];
        $reader = new Reader($method->getDeclaringClass()->getName(), $this->namespaces);

        $classOptions = $reader->getClassAnnotations(Option::class);
        $methodOptions = $reader->getMethodAnnotations($method->getName(), Option::class);

        foreach ([$classOptions, $methodOptions] as $optionList) {
            foreach ($optionList as $option) {
                if ($option->name == Option::MULTIPLE) {
                    $options = array_merge($options, $option->value);
                } else {
                    $options[$option->name] = $option->value;
                }
            }
        }

        return $options;
    }
}
