<?php

namespace Pipa\ORM;
use Pipa\ORM\Descriptor\AnnotationDescriptorProvider;
use Pipa\ORM\Descriptor\ClassDescriptor;

ClassDescriptor::addProvider(new AnnotationDescriptorProvider());
