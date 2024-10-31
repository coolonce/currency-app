<?php

namespace App\Interfaces;

interface MessageBrokerInterface
{
    public function sendMessage(string $message): void;
    public function consume(callable $callback): void;
    public function declareQueue(string $queueName): void;
}