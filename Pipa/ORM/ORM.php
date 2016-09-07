<?php

namespace Pipa\ORM;
use Pipa\Data\ConnectionManager;
use Pipa\ORM\Descriptor\ClassDescriptor;
use Pipa\ORM\Mapper\RelationalMapper;
use Pipa\ORM\Query\ORMCriteria;

abstract class ORM {

    protected static $namespacedDataSources = [];

	protected static $instanceCache;

    static function cast(Entity $instance, array $record, array $hints = []) {
        if (!$hints) {
            $hints = self::getCastingHints(ClassDescriptor::forClass(get_class($instance)));
		}
    }

    static function getCastingHints(ClassDescriptor $classDescriptor) {
        $hints = ["set"=>[], "transform"=>[], "bring"=>[], "unset"=>[]];

        foreach ($classDescriptor->properties as $propertyName=>$propertyDescriptor) {
            $relation = $classDescriptor->hasRelation($propertyName);

            if ($relation) {

            }
        }

        return $hints;
    }

    static function getCriteria($class) {
        if (is_object($class))
            $class = get_class($class);

        $dataSource = self::getDataSource($class);
        $descriptor = self::getDescriptor($class);
        return new ORMCriteria($dataSource, $descriptor);
    }

    static function getDataSource($class) {
        if (is_object($class))
            $class = get_class($class);

        foreach (self::$namespacedDataSources as $ns=>$ds)
            if (strpos(static::class, "$ns\\") === 0)
                return ConnectionManager::get($ds);

        $dataSource = ClassDescriptor::forClass($class)->dataSource;
        return ConnectionManager::get($dataSource);
    }

    static function getDescriptor($class) {
        if (is_object($class))
            $class = get_class($class);

        return ClassDescriptor::forClass($class);
    }

	static function getInstanceCache($class) {
		if (!self::$instanceCache)
			self::$instanceCache = new InstanceCache();

		return self::$instanceCache;
	}

    static function getMapper($class) {
        return new RelationalMapper();
    }
}
