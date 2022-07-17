<?php

namespace TeyvatPS\utils;

class AvatarUtils
{
    public static function getHashByPreSuf(int $prefix, int $suffix): int
    {
        return ($prefix << 32) | $suffix;
    }

    public static function getAbilityHash(string $ability): int
    {
        $v7 = 0;
        $v8 = 0;
        while ($v8 < strlen($ability)) {
            $v7 = AvatarUtils::thirtyTwoBitIntval(
                ord($ability[$v8++]) + 131 * $v7
            );
        }

        return $v7;
    }

    private static function thirtyTwoBitIntval(float $value): int|float
    {
        if ($value < -2147483648) {
            return -(-($value) & 0xffffffff);
        } elseif ($value > 2147483647) {
            return ($value & 0xffffffff);
        }

        return $value;
    }
}
