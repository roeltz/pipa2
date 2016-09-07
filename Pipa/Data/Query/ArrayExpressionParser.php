<?php

namespace Pipa\Data\Query;
use Pipa\Data\Exception\CriteriaException;
use Pipa\Registry\Registry;

class ArrayExpressionParser {

    static function expression($operator, $a, $b) {
        $expression = Registry::get(self::class, "operator$operator", $a, $b);
        if ($expression) {
            return $expression;
        } else {
            throw new CriteriaException("Invalid criteria operator '$operator'");
        }
    }

    static function operator($operator, callable $callable) {
        Registry::setConstructor(self::class, "operator$operator", $callable);
    }

    static function parse(array $expressions) {
        $parsed = [];
        foreach ($expressions as $expression)
            $parsed[] = self::parseSingle($expression);
        return $parsed;
    }

    static function parseSingle($expression) {
        if (is_array($expression)) {
            switch (count($expression)) {
                case 3:
                    list($a, $operator, $b) = $expression;
                    return self::expression($operator, $a, $b);
                case 2:
                    list($operator, $a) = $expression;
                    return self::expression($operator, $a, null);
                default:
                    throw new CriteriaException("Cannot parse array expression ".json_encode($expression));
            }
        } else if ($expression instanceof Expression) {
            return $expression;
        }
    }
}
