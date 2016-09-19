<?php

namespace Pipa\ORM\Descriptor;
use ReflectionClass;
use ReflectionProperty;
use Pipa\Annotation\Reader;
use Pipa\ORM\Exception\DescriptorException;

class AnnotationDescriptorProvider implements DescriptorProvider {

    function provideForClass($class) {
        $descriptor = new ClassDescriptor($class);
        $reader = new Reader($class, ["Pipa\\ORM\\Annotation"]);
        $class = new ReflectionClass($class);

        $this->surveyClass($descriptor, $reader);

        foreach ($class->getProperties() as $property)
			if ($property->isPublic())
            	$this->surveyProperty($descriptor, $property, $reader);

        return $descriptor;
    }

    function surveyClass(ClassDescriptor $descriptor, Reader $reader) {

        if ($reader->getClassAnnotation("Embedded")) {
            $descriptor->embedded = true;
        } else {
            if ($collection = $reader->getClassAnnotation("Collection")) {
                $descriptor->collection = $collection->value;
            } else {
                throw new DescriptorException("Collection not set for class '{$descriptor->class}'");
            }

            if ($dataSource = $reader->getClassAnnotation("DataSource"))
                $descriptor->dataSource = $dataSource->value;
        }

    }

    function surveyProperty(ClassDescriptor $descriptor, ReflectionProperty $property, Reader $reader) {
        $name = $property->getName();

        if ($reader->getPropertyAnnotation($name, "Ignore")) return;

        $pd = new PropertyDescriptor($name);

        $pd->cascaded = !!$reader->getPropertyAnnotation($name, "Cascade");
        $pd->eager = !!$reader->getPropertyAnnotation($name, "Eager");
		$pd->embedded = !!$reader->getPropertyAnnotation($name, "Embedded");
        $pd->generated = !!$reader->getPropertyAnnotation($name, "Generated");
        $pd->notNull = !!$reader->getPropertyAnnotation($name, "NotNull");
        $pd->pk = !!$reader->getPropertyAnnotation($name, "Id");

        if ($alias = $reader->getPropertyAnnotation($name, "AliasOf"))
            $pd->underlyingName = [$alias->value];

        if ($computed = $reader->getPropertyAnnotation($name, "Computed")) {
            $pd->computedBy = $computed->value;
            $pd->persistent = false;
        }

        if ($order = $reader->getPropertyAnnotation($name, "OrderByDefault"))
            $pd->orderByDefault = $order->value;

        if ($transformations = $reader->getPropertyAnnotations($name, "Transform"))
            foreach ($transformations as $t)
                $pd->transformations[$t->value] = (array) $t;

        $descriptor->addProperty($pd);

        if ($many = $reader->getPropertyAnnotation($name, "Many"))
            $descriptor->addRelation(new MultipleRelationDescriptor($name, $descriptor->normalizeClass($many->class), (array) $many->fk, (array) $many->orderBy, (array) $many->where, $many->path));

        if ($one = $reader->getPropertyAnnotation($name, "One"))
            $descriptor->addRelation(new RelationDescriptor($name, $descriptor->normalizeClass($one->class), (array) $one->fk));
    }
}
