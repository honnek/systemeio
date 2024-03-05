<?php

namespace App\Entity\Enum;

enum PaymentProcessor: string
{
    case PAYPAL = 'paypal';

    case STRIPE = 'stripe';

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
