<?php

namespace App\Entity\Enum;

enum Currency: string
{
    /** Eвро */
    case EURO = 'EURO';

    /** Доллар */
    case DOLLAR = 'DOLLAR';
}