<?php

namespace Pipa\Util;

class String {

    static function fill($str, array $values) {
    	return self::interpolate($str, function($key) use(&$values) {
    		return @$values[$key];
    	}, $str);
    }

    static function interpolate($str, $callback) {
        return preg_replace_callback('/\{([^}]+)\}/', function($m) use($callback){
    		return $callback($m[1]);
    	}, $str);
    }

    static function plural($forms, $n) {
        $forms = explode("|", $forms);
        $simple = count($forms) == 2;
        foreach ($forms as $i=>$form) {
            if ($form = self::matchesPlural($form, $n, $simple ? $i : false))
                return $form;
        }
    }

    static function matchesPlural($form, $n, $i) {
        if (preg_match($pattern = '/^\[(\d+)\]\s/', $form, $m) && $n == $m[1]) {
            return preg_replace($pattern, '', $form);
        } elseif (preg_match($pattern = '/^\[(-?\d+|\*)\s*,\s*(-?\d+|\*)\]\s/', $form, $m)) {
            list(, $lower, $upper) = $m;
            if ((!is_numeric($lower) || $n >= $lower) && (!is_numeric($upper) || $n <= $upper))
                return preg_replace($pattern, '', $form);
        } elseif (is_integer($i) && ($n == 1 && $i == 0) || ($n != 1 && $i == 1)) {
            return $form;
        }
        return false;
    }
	
}
