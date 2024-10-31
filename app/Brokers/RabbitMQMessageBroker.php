<?php

namespace App\Brokers;

use App\Interfaces\MessageBrokerInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQMessageBroker implements MessageBrokerInterface
{
    private $connection;
    private $channel;
    private $queueName;

    public function __construct($host, $port, $user, $password, $queueName)
    {
        $this->connection = new AMQPStreamConnection($host, $port, $user, $password);
        $this->channel    = $this->connection->channel();
        $this->queueName  = $queueName;
    }

    public function declareQueue(string $queueName): void
    {
        $this->channel->queue_declare($queueName, false, true, false, false);
    }

    public function sendMessage(string $message): void
    {
        $amqpMessage = new AMQPMessage($message, [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
        ]);

        try {
            // Отправка сообщения в очередь
            $this->channel->basic_publish($amqpMessage, '', $this->queueName);
            echo "Message sent to queue: {$this->queueName}" . PHP_EOL;
        } catch (\Exception $e) {
            echo "Failed to send message: " . $e->getMessage() . PHP_EOL;
        }
    }

    public function consume(callable $callback): void
    {
        $this->channel->basic_consume($this->queueName, '', false, true, false, false, $callback);

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
}