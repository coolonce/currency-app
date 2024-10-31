<?php

use App\Brokers\RabbitMQMessageBroker;
use App\Commands\FetchDataCommand;
use App\Components\RedisCache;
use App\Interfaces\CacheInterface;
use App\Interfaces\CurrencyProviderInterface;
use App\Interfaces\MessageBrokerInterface;
use App\Providers\CbrCurrencyProvider;
use App\Services\CurrencyRateService;
use App\Workers\Worker;
use DI\Container;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$container = new Container();
// Регестрируем
$container->set(CacheInterface::class, function () {
    return new RedisCache();
});

$container->set(MessageBrokerInterface::class, function () {
    return new RabbitMQMessageBroker(
        $_ENV['RABBITMQ_HOST'],
        $_ENV['RABBITMQ_PORT'],
        $_ENV['RABBITMQ_USER'],
        $_ENV['RABBITMQ_PASSWORD'],
        $_ENV['RABBITMQ_QUEUE']
    );
});

$container->set(CurrencyProviderInterface::class, function () {
    return new CbrCurrencyProvider();
});

$container->set(CurrencyRateService::class, function ($c) {
    return new CurrencyRateService(
        $c->get(CurrencyProviderInterface::class),
        $c->get(CacheInterface::class),
    );
});

$container->set(FetchDataCommand::class, function($c) {
    return new FetchDataCommand(
        $c->get(CurrencyRateService::class),
        $c->get(MessageBrokerInterface::class)
    );
});

$container->set(Worker::class, function ($c) {
    return new Worker(
        $c->get(MessageBrokerInterface::class),
        $c->get(CacheInterface::class)
    );
});

return $container;