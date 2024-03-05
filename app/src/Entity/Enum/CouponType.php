<?php

namespace App\Entity\Enum;

enum CouponType: string
{
    /** Купон с фиксированной скидкой от суммы покупки */
    case FIXED = 'FIXED';

    /** Купон с процентной скидкой от суммы покупки */
    case PERCENT = 'PERCENT';

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}