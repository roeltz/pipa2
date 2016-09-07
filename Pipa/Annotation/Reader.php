<?php

namespace Pipa\Annotation;
use Pipa\Parse\Parser;
use ReflectionClass;

class Reader extends Parser {

	protected $cache = [];

	protected $class;

	protected $namespaces;

	function __construct($class, $namespaces = [""]) {
		parent::__construct(new Grammar);
		$this->class = new ReflectionClass($class);
		$this->namespaces = array_merge($namespaces);
	}

	function findAnnotationClass($class) {
		if (class_exists($class))
			return $class;

		foreach ($this->namespaces as $ns)
			if (class_exists($fqn = "$ns\\$class"))
				return $fqn;
	}

	function getClassAnnotation($annotationClass) {
		$annotations = $this->getClassAnnotations($annotationClass);
		return @$annotations[0];
	}

    function getClassAnnotations($annotationClass = null) {
		return $this->filter($this->getAllClassAnnotations(), $annotationClass);
    }

	function getAllClassAnnotations() {
		if (isset($this->cache["class"]))
			return $this->cache["class"];

		$source = $this->class->getDocComment();
		return $this->cache["class"] = $this->parse($source);
	}

	function getMethodAnnotation($method, $annotationClass) {
		$annotations = $this->getMethodAnnotations($method, $annotationClass);
		return @$annotations[0];
	}

	function getMethodAnnotations($method, $annotationClass = null) {
		return $this->filter($this->getAllMethodAnnotations($method), $annotationClass);
	}

	function getAllMethodAnnotations($method) {
		if (isset($this->cache["method"][$method]))
			return $this->cache["method"][$method];

		$source = $this->class->getMethod($method)->getDocComment();
		return $this->cache["method"][$method] = $this->parse($source);
	}

	function getPropertyAnnotation($property, $annotationClass) {
		$annotations = $this->getPropertyAnnotations($property, $annotationClass);
		return @$annotations[0];
	}

	function getPropertyAnnotations($property, $annotationClass = null) {
		return $this->filter($this->getAllPropertyAnnotations($property), $annotationClass);
	}

	function getAllPropertyAnnotations($property) {
		if (isset($this->cache["property"][$property]))
			return $this->cache["property"][$property];

		$source = $this->class->getProperty($property)->getDocComment();
		return $this->cache["property"][$property] = $this->parse($source);
	}

	function parse($source) {
		return array_filter(array_map(function($a){
			if ($class = $this->findAnnotationClass($a["class"]))
				return new $class($a["parameters"]);
		}, parent::parse($source)));

	}

	protected function filter($annotations, $annotationFilter) {
		if ($annotationFilter) {
			$annotationClass = $this->findAnnotationClass($annotationFilter);

			if (!$annotationClass)
				throw new AnnotationException("Annotation '$annotationFilter' does not correspond to an existing class");

			$annotations = array_values(array_filter($annotations, function($annotation) use($annotationClass){
				return $annotation instanceof $annotationClass;
			}));
		}
		return $annotations;
	}
}
