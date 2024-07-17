<?php

namespace App\HttpClient\CurrencyRateSource;

use App\HttpClient\CurrencyRateSource\CurrencyRatesSourceHttpClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class BaseRatesSourceHttpClient implements CurrencyRatesSourceHttpClientInterface
{

    protected string $getCurrencyRatesMethod;

    protected string $getCurrencyRatesUrl;

    protected string $allowedListEnum;

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        private string $baseUrl
    ) {
    }

    public function getCurrencyRates(string $currencyCode): array
    {
        if (!$this->isValid($currencyCode)) {
            throw new \RuntimeException('Invalid currency');
        }

        $url = sprintf($this->getCurrencyRatesUrl, $this->baseUrl, $currencyCode);

        try {

            $response = $this->httpClient->request($this->getCurrencyRatesMethod, $url);

            return $this->prepareRatesData($response->toArray(), $currencyCode);
        } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {

            $this->logger->error('Failed to fetch currency rates', [
                'currency_code' => $currencyCode,
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException('Failed to fetch currency rates');
        }
    }

    abstract protected function prepareRatesData(array $data, string $currencyCode);

    protected function isValid(string $currencyCode): bool
    {
        return !empty($this->allowedListEnum::tryFrom($currencyCode));
    }
}
