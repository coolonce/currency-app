<?php

namespace App\Commands;

use App\Interfaces\MessageBrokerInterface;
use App\Services\CurrencyRateService;
use DateTime;

class FetchDataCommand
{

    public function __construct(
        public CurrencyRateService    $currencyRateService,
        public MessageBrokerInterface $messageBroker
    ) {}

    public function execute(int $days): void
    {
        $currentDate = new DateTime();
        for ($i = 0; $i <= $days; $i++) {
            $date = $currentDate->format('d.m.Y');
            $data        = [
                'date' => $date,
                'data' => $this->currencyRateService->getCurrencyValues($date)
            ];

            $this->messageBroker->sendMessage(json_encode($data));
            $currentDate->modify('-1 day');
        }
    }
}