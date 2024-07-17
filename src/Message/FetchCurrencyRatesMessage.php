<?php

namespace App\Message;

final class FetchCurrencyRatesMessage
{
    public function __construct(
        public readonly string $currencyCode,
    ) {
    }
}
