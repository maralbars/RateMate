<?php

namespace App\Utils;

use App\Enum\CoinPaprikaAllowedCurrenciesEnum;
use App\Enum\FloatRatesAllowedCurrenciesEnum;

class Currency
{

    private static array $allowedCurrencies;

    public static function isAllowed($currencyCode): bool
    {
        if (empty(self::$allowedCurrencies)) {
            self::$allowedCurrencies = array_unique(array_map(fn ($item) => $item->value, array_merge(FloatRatesAllowedCurrenciesEnum::cases(), CoinPaprikaAllowedCurrenciesEnum::cases())));
        }

        return in_array($currencyCode, self::$allowedCurrencies);
    }

    public static function convert(float $amount, float $currencyFromRate, float $currencyToRate): array
    {
        return [
            'initial_amount' => $amount,
            'amount' => $amount * ($currencyFromRate / $currencyToRate),
            'currency_from' => $currencyFromRate,
            'currency_to' => $currencyToRate
        ];
    }
}
