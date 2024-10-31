<?php

namespace App\Services;

use App\Interfaces\CacheInterface;
use App\Interfaces\CurrencyProviderInterface;

class CurrencyRateService
{


    public function __construct(
        public CurrencyProviderInterface $provider,
        public CacheInterface            $cache
    ) {}


    public function getCurrencyValues(string $date): array
    {
        $cacheKey = "currency_values-by-{$date}";
        if ($this->cache->exists($cacheKey) > 1) {
            return $this->cache->get($cacheKey);
        }
        return $this->provider->getDailyCurrencyValue($date);
    }

//    public function getCachedRate(string $date, string $currencyCode, string $baseCurrency): float
//    {
//        $cacheKey = "currency-rate:{$date}-$currencyCode-{$baseCurrency}";
//        if ($cached = $this->cache->get($cacheKey)) {
//            return (float)$cached;
//        }
//
//        // Получаем курс с помощью провайдера
//        $rate = $this->provider->getExchangeRate($date, $currencyCode, $baseCurrency);
//
//        // Сохраняем в кэш
//        $this->cache[$date][$currencyCode] = $rate;
//
//        return $rate;
//    }
//
//    public function getRateWithDifference(string $date, string $currencyCode, string $baseCurrency = 'RUR'): array
//    {
//        // Получаем текущий курс на указанную дату
//        $currentRate = $this->getCachedRate($date, $currencyCode, $baseCurrency);
//
//        // Определяем предыдущий торговый день
//        $previousDate = $this->getPreviousTradingDay($date);
//
//        // Получаем курс на предыдущий торговый день
//        $previousRate = $this->getCachedRate($previousDate, $currencyCode, $baseCurrency);
//
//        // Рассчитываем разницу с предыдущим днём
//        $difference = $currentRate - $previousRate;
//
//        return [
//            'rate'       => $currentRate,
//            'difference' => $difference,
//        ];
//    }
//
//    /**
//     * Получаем курс валюты с использованием кэша для оптимизации запросов.
//     */
//
//
//    /**
//     * Определение предыдущего торгового дня.
//     */
//    protected function getPreviousTradingDay(string $date): string
//    {
//        $dateTime = new \DateTime($date);
//        do {
//            // Переходим на предыдущий день
//            $dateTime->modify('-1 day');
//        } while (in_array($dateTime->format('N'), [6, 7])); // Пропускаем выходные (субботу и воскресенье)
//
//        return $dateTime->format('Y-m-d');
//    }
}
