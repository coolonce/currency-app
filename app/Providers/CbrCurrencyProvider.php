<?php

namespace App\Providers;

use App\Interfaces\CacheInterface;
use App\Interfaces\CurrencyProviderInterface;
use Predis\Client as RedisClient;

class CbrCurrencyProvider implements CurrencyProviderInterface
{
    public function __construct(
    ) {}

    public function getDailyCurrencyValue(string $date): array
    {
        $response = file_get_contents("https://www.cbr.ru/scripts/XML_daily.asp?date_req={$date}");
        $xml        = simplexml_load_string($response);
        $ratesArray = json_decode(json_encode($xml), true)['Valute'];
        $result = [];
        foreach ($ratesArray as $rate) {
            $result[$rate['CharCode']] = $rate['Value'];
        }

        return $result;
    }

//    public function getExchangeRate(string $date, string $currencyCode, string $baseCurrency = 'RUR'): float
//    {
//        $cacheKey   = "currency_rates_{$date}_{$currencyCode}_{$baseCurrency}";
//        $cachedItem = $this->redis->get($cacheKey);
//
//        if ($cachedItem->isHit()) {
//            return $cachedItem->get();
//        }
//
//        $response   = $this->httpClient->get("https://www.cbr.ru/scripts/XML_daily.asp?date_req={$date}");
//        $xml        = simplexml_load_string($response->getBody());
//        $ratesArray = json_decode(json_encode($xml), true)['Valute'];
//
//        $currencyRateToRUR = null;
//        $baseRateToRUR     = $baseCurrency === 'RUR' ? 1.0 : null;
//
//        foreach ($ratesArray as $valute) {
//            if ($valute['CharCode'] === $currencyCode) {
//                $currencyRateToRUR = (float)str_replace(',', '.', $valute['Value']);
//            }
//            if ($baseCurrency !== 'RUR' && $valute['CharCode'] === $baseCurrency) {
//                $baseRateToRUR = (float)str_replace(',', '.', $valute['Value']);
//            }
//        }
//
//        if ($currencyRateToRUR === null || ($baseCurrency !== 'RUR' && $baseRateToRUR === null)) {
//            throw new \Exception("Rate for $currencyCode/$baseCurrency not found for date $date");
//        }
//
//        $rate = $currencyRateToRUR / $baseRateToRUR;
//        $this->cache->save($cachedItem->set($rate));
//
//        return $rate;
//    }
}
