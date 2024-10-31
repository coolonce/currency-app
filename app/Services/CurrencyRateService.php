<?php

namespace App\Services;

use App\Constans\CacheConstants;
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
        $cacheKey = CacheConstants::KEY_CURRENCY_VALUES . $date;

        if ($this->cache->exists($cacheKey) > 1) {
            return $this->cache->get($cacheKey);
        }

        return $this->provider->getDailyCurrencyValue($date);
    }

    public function getCachedCurrencyRate(string $date, string $currency, string $baseCurrency = 'RUR'): float
    {
        $cacheKey = CacheConstants::KEY_CURRENCY_RATE . "{$date}-{$currency}-{$baseCurrency}";
        if ($this->cache->exists($cacheKey)) {
            return (float) $this->cache->get($cacheKey);
        }

        $rate = $this->getCurrencyRate($date, $currency, $baseCurrency);

        $this->cache->set($cacheKey, $rate);
        return $rate;
    }

    public function getCurrencyRate(string $date, string $currency, string $baseCurrency = 'RUB'): float
    {
        $cacheKey = CacheConstants::KEY_CURRENCY_VALUES . $date;

        if ($this->cache->exists($cacheKey) === 0) {
//              Можно добавить запуск фетчера для холодного старта, мы учитываем что кеш прогреваем перед стартом
            return 0.0;
        }

        $currencyByDateValues = json_decode($this->cache->get($cacheKey), true);

        $baseRate     = $baseCurrency === 'RUB' ? 1.0 : (float)str_replace(',', '.', $currencyByDateValues[$baseCurrency]);
        $currencyRate = (float)str_replace(',', '.', $currencyByDateValues[$currency]);

        return $currencyRate / $baseRate;
    }

    public function getRateWithDifference(string $date, string $currencyCode, string $baseCurrency = 'RUR'): array
    {
        $currentRate = $this->getCachedCurrencyRate($date, $currencyCode, $baseCurrency);

        $previousDate = $this->getPreviousTradingDay($date);

        $previousRate = $this->getCachedCurrencyRate($previousDate, $currencyCode, $baseCurrency);

        // Рассчитываем разницу с предыдущим днём
        $difference = $currentRate - $previousRate;

        return [
            'rate'       => round($currentRate, 3),
            'difference' => round($difference, 3),
        ];
    }

    /**
     * Определение предыдущего торгового дня.
     */
    protected function getPreviousTradingDay(string $date): string
    {
        $dateTime = new \DateTime($date);
        do {
            $dateTime->modify('-1 day');
        } while (in_array($dateTime->format('N'), [6, 7])); // Пропускаем выходные (субботу и воскресенье)

        return $dateTime->format('d.m.Y');
    }
}
