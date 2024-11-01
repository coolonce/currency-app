<?php

namespace App\Brokers;

use App\Interfaces\MessageBrokerInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQMessageBroker implements MessageBrokerInterface
{
    private const MAX_RETRIES = 3;
    private const INITIAL_DELAY_MS = 100;
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

        $attempt = 0;
        try {
            $this->channel->basic_publish($amqpMessage, '', $this->queueName);
            echo "Message sent to queue: {$this->queueName}" . PHP_EOL;
            return;
        } catch (\Exception $e) {
            $attempt++;
            echo "Attempt {$attempt}: Failed to send message - " . $e->getMessage() . PHP_EOL;

            if ($attempt >= self::MAX_RETRIES) {
                echo "All attempts failed. Logging the message for manual processing." . PHP_EOL;
                $this->logFailedMessage($message);
                return;
            }

            $this->applyBackoffDelay($attempt);
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

    private function applyBackoffDelay(int $attempt): void
    {
        //Экспоненциальная задержка.
        $delayMs = self::INITIAL_DELAY_MS * (2 ** ($attempt - 1));
        usleep($delayMs * 1000);
    }

    private function logFailedMessage(string $message): void
    {
        file_put_contents('./failed_messages.log', $message . PHP_EOL, FILE_APPEND);
    }
}