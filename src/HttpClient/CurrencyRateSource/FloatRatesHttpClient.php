<?php

namespace App\HttpClient\CurrencyRateSource;

use App\Enum\FloatRatesAllowedCurrenciesEnum;
use App\HttpClient\CurrencyRateSource\CurrencyRatesSourceHttpClientInterface;
use  App\HttpClient\CurrencyRateSource\BaseRatesSourceHttpClient;

class FloatRatesHttpClient extends BaseRatesSourceHttpClient implements CurrencyRatesSourceHttpClientInterface
{
    protected string $getCurrencyRatesMethod = 'GET';

    protected string $getCurrencyRatesUrl = '%s/daily/%s.json';

    protected string $allowedListEnum = FloatRatesAllowedCurrenciesEnum::class;

    protected function prepareRatesData(array $data, string $currencyCode)
    {
        $result = [];
        foreach ($data as $item) {
            $result[$item['code']] = [
                'rate' => $item['rate'],
                'source' => 'floatrates',
            ];
        }
        return $result;
    }
}
