<?php

namespace TeyvatPS\data\props;

use PropPair;
use PropValue;

class EntityProperties
{
    public const PROP_EXP = 1001;

    public const PROP_BREAK_LEVEL = 1002;

    public const PROP_SATIATION_VAL = 1003;

    public const PROP_SATIATION_PENALTY_TIME = 1004;

    public const PROP_LEVEL = 4001;

    public static function getAll(): array
    {
        return [
            self::PROP_LEVEL => (new PropValue)->setType(
                self::PROP_LEVEL
            )->setVal(90)->setIval(90),
            self::PROP_EXP => (new PropValue)->setType(
                self::PROP_EXP
            )->setVal(0)->setIval(0),
            self::PROP_BREAK_LEVEL => (new PropValue)->setType(
                self::PROP_BREAK_LEVEL
            )->setVal(6)->setIval(6),
            self::PROP_SATIATION_PENALTY_TIME => (new PropValue)->setType(
                self::PROP_SATIATION_PENALTY_TIME
            )->setVal(0)->setIval(0),
            self::PROP_SATIATION_VAL => (new PropValue)->setType(
                self::PROP_SATIATION_VAL
            )->setVal(0)->setIval(0),
        ];
    }

    public static function toPropPair(int $prop, int $value = 0): PropPair
    {
        return (new PropPair)->setType($prop)->setPropValue(
            self::toProto($prop, $value)
        );
    }

    public static function toProto(int $prop, int $value = 0): PropValue
    {
        return (new PropValue)->setType($prop)->setVal($value)->setIval(
            $value
        );
    }
}
