<?php

namespace App\Entity\Enum;

enum CountryCode: string
{
    /** Германия */
    case GERMANY = 'DE';

    /** Италия */
    case ITALY = 'IT';

    /** Греция */
    case GREECE = 'GR';

    /** Франция */
    case FRANCE = 'FR';

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
