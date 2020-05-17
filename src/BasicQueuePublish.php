<?php

declare(strict_types=1);

namespace AmqpBasic;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class BasicQueuePublish
{
    private AMQPStreamConnection $connection;
    private string $queueName;

    public function __construct(AMQPStreamConnection $connection, string $queueName)
    {
        $this->connection = $connection;
        $this->queueName = $queueName;
    }

    public function publish(string $message): void
    {
        $channel = $this->connection->channel();
        $channel->queue_declare($this->queueName, false, false, false, false);
        $channel->basic_publish(new AMQPMessage($message), '', $this->queueName);
        $channel->close();
        $this->connection->close();
    }
}
