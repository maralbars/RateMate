<?php

namespace App\Enum;

enum BaseCurrencyEnum: string
{
    case USD = 'USD';
    case EUR = 'EUR';
    case GBP = 'GBP';
    case JPY = 'JPY';
    case KZT = 'KZT';
    // Добавьте другие валюты по мере необходимости

    public static function getDefault(): self
    {
        return self::USD;
    }
}
