<?php

namespace App\HttpClient\CurrencyRateSource;

use App\Enum\CoinPaprikaAllowedCurrenciesEnum;
use App\HttpClient\CurrencyRateSource\CurrencyRatesSourceHttpClientInterface;
use  App\HttpClient\CurrencyRateSource\BaseRatesSourceHttpClient;

class CoinPaprikaHttpClient extends BaseRatesSourceHttpClient implements CurrencyRatesSourceHttpClientInterface
{
    protected string $getCurrencyRatesMethod = 'GET';

    protected string $getCurrencyRatesUrl = '%s/exchanges/coinbase/markets?quotes=%s';

    protected string $allowedListEnum = CoinPaprikaAllowedCurrenciesEnum::class;

    protected function prepareRatesData(array $data, string $currencyCode)
    {
        $result = [];
        foreach ($data as $item) {
            $code =  explode('/', $item['pair'], 2)[0];
            $result[$code] = [
                'rate' => (string) 1/$item['quotes'][$currencyCode]['price'],
                'source' => 'coinpaprika',
            ];
        }
        return $result;
    }
}
