<?php

namespace App\Workers;

use App\Interfaces\CacheInterface;
use App\Interfaces\MessageBrokerInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Worker
{
    public function __construct(
        public MessageBrokerInterface $messageBroker,
        public CacheInterface         $cache
    )
    {
        $this->messageBroker->declareQueue($_ENV['RABBITMQ_QUEUE']);
    }

    public function processMessages()
    {
        $callback = function (AMQPMessage $msg) {
            $messageData = json_decode($msg->getBody(), true);
            $date        = $messageData['date'];
            $data        = $messageData['data'];
            echo "Processing message with delivery tag: " . $msg->getDeliveryTag() . PHP_EOL;
            try {
                $this->cache->set($date, json_encode($data));
            } catch (\Throwable $t) {
                echo "Error processing message for {$date}: " . $t->getMessage() . PHP_EOL;
                $msg->nack();
            }

        };

        $this->messageBroker->consume($callback);
    }
}