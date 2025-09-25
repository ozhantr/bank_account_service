<?php

namespace App\Util;

use InvalidArgumentException;

final class Money
{
    public static function decimalToCents(string $decimal): int
    {
        // "12.34" -> 1234 ; "10" -> 1000
        // to do: regex validation
        // if (!preg_match('//', $decimal)) {
        //     throw new InvalidArgumentException('Invalid money format');
        // }

        $parts = explode('.', $decimal, 2);
        $euros = (int) $parts[0];
        $cents = isset($parts[1]) ? mb_str_pad($parts[1], 2, '0') : '00';

        return $euros * 100 + (int) $cents;
    }
}
