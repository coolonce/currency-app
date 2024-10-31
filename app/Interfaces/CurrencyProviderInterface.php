<?php

namespace App\Interfaces;

interface CurrencyProviderInterface
{
    public function getDailyCurrencyValue(string $date): array;
}
