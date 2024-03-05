<?php

namespace App\Entity\Enum;

/**
 * Возвращает процент налога за покупку в каждой стране
 */
enum TaxPercent: int
{
    /** Германия */
    case GERMANY = 19;

    /** Италия */
    case ITALY = 22;

    /** Греция */
    case GREECE = 24;

    /** Франция */
    case FRANCE = 20;

    /**
     * @param string $country
     * @return TaxPercent|null
     */
    public static function getByCountry(string $country): ?TaxPercent
    {
        $className = get_called_class();
        $constName = $className . '::' . $country;

        if (defined($constName)) {
            return constant($constName);
        }

        return null;
    }
}