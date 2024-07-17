<?php

namespace App\MessageHandler;

use App\Message\FetchCurrencyRatesMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Service\ExchangeRateService;


#[AsMessageHandler]
final class FetchCurrencyRatesMessageHandler
{
    public function __construct(private ExchangeRateService $rateService)
    {
    }
    public function __invoke(FetchCurrencyRatesMessage $message): void
    {
        $this->rateService->fetchAndSaveRates($message->currencyCode);
    }
}
