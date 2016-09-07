<?php

namespace Pipa\Data\Query;
use Pipa\Data\Field;
use Pipa\Data\Query\ArrayExpressionParser as AEP;
use Pipa\Data\Query\ComparissionExpression as CE;
use Pipa\Data\Query\JunctionExpression as JE;

AEP::operator("=", function($a, $b){
    return new CE(CE::EQ, Field::from($a), $b);
});

AEP::operator("<>", function($a, $b){
    return new CE(CE::NE, Field::from($a), $b);
});

AEP::operator("<", function($a, $b){
    return new CE(CE::LT, Field::from($a), $b);
});

AEP::operator("<=", function($a, $b){
    return new CE(CE::LE, Field::from($a), $b);
});

AEP::operator(">", function($a, $b){
    return new CE(CE::GT, Field::from($a), $b);
});

AEP::operator(">=", function($a, $b){
    return new CE(CE::GE, Field::from($a), $b);
});

AEP::operator("in", function($a, $b){
    return new ListExpression(ListExpression::IN, Field::from($a), $b);
});

AEP::operator("not in", function($a, $b){
    return new ListExpression(ListExpression::NOT_IN, Field::from($a), $b);
});

AEP::operator("like", function($a, $b){
    return new CE(CE::LIKE, Field::from($a), $b);
});

AEP::operator("ilike", function($a, $b){
    return new CE(CE::ILIKE, Field::from($a), $b);
});

AEP::operator("regex", function($a, $b){
    return new CE(CE::REGEX, Field::from($a), $b);
});

AEP::operator("and", function(...$e){
    return new JE(JE::CONJUNCTION, AEP::parse($e));
});

AEP::operator("or", function(...$e){
    return new JE(JE::DISJUNCTION, AEP::parse($e));
});

AEP::operator("between", function($a, $b){
    return new RangeExpression(Field::from($a), $b[0], $b[1]);
});
