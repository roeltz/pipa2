<?php

namespace Pipa\Util;

abstract class Arrays {

	static function remove(array &$array, $value) {
		foreach($array as $i=>$v)
			if ($v === $value)
				unset($array[$i]);
		return $array;
	}

}
