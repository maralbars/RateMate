<?php

namespace App\Service;

use App\HttpClient\CurrencyRateSource\CoinPaprikaHttpClient;
use App\HttpClient\CurrencyRateSource\FloatRatesHttpClient;
use App\HttpClient\CurrencyRateSource\CurrencyRatesSourceHttpClientInterface;
use App\Service\FileService;
use App\Utils\Currency;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExchangeRateService
{
    private string $currencyCode;

    private array $currencyRates;

    public function __construct(
        private CoinPaprikaHttpClient $coinPaprikaClient,
        private FloatRatesHttpClient $floatRatesClient,
        private FileService $fileService,
    ) {
    }

    public function setCurrency(string $currencyCode): void
    {
        $this->currencyCode = $currencyCode;
        $this->fileService->setFileName($currencyCode);
        $this->currencyRates = $this->fileService->getJson();
    }

    public function fetchAndSaveRates(string $currencyCode): array
    {
        $this->setCurrency($currencyCode);

        $result = [
            'coinpaprika' => $this->fetchRates($this->coinPaprikaClient),
            'floatrates' => $this->fetchRates($this->floatRatesClient),
        ];

        $this->save();

        return $result;
    }

    public function convert(float $amount, string $from, string $to): array
    {
        $result = [];

        if (!(Currency::isAllowed($from) || Currency::isAllowed($to))) {
            throw new NotFoundHttpException('Invalid currency code');
        }

        $rates = $this->fileService->getJson($from);

        if (!empty($rates) && array_key_exists($to, $rates)) {
            $result = Currency::convert($amount, $rates[$to]['rate'], 1);
        } else {
            $rates = $this->fileService->getJson($to);

            if (!empty($rates) && array_key_exists($from, $rates)) {
                $result = Currency::convert($amount, 1, $rates[$from]['rate']);
            } else {
                throw new NotFoundHttpException('Currencies\' rates are not found. Try to load them.');
            }
        }

        return $result;
    }

    private function fetchRates(CurrencyRatesSourceHttpClientInterface &$client): bool
    {
        try {
            $this->currencyRates = array_merge(
                $this->currencyRates,
                $client->getCurrencyRates($this->currencyCode)
            );
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    private function save(): void
    {
        if (!empty($this->currencyRates)) {
            $this->fileService->saveJson($this->currencyRates);
        }
    }
}
