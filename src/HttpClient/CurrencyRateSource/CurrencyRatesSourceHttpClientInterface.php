<?php

namespace App\HttpClient\CurrencyRateSource;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

interface CurrencyRatesSourceHttpClientInterface
{
    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger, string $baseUrl);

    public function getCurrencyRates(string $currencyCode): array;
}
