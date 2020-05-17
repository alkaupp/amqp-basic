<?php

declare(strict_types=1);

namespace AmqpBasic;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class BasicQueueConsume
{
    private AMQPStreamConnection $connection;
    private string $queueName;

    public function __construct(AMQPStreamConnection $connection, string $queueName)
    {
        $this->connection = $connection;
        $this->queueName = $queueName;
    }

    public function consume(callable $callback)
    {
        $channel = $this->connection->channel();

        $channel->queue_declare($this->queueName, false, false, false, false);

        $channel->basic_consume(
            $this->queueName,
            '',
            false,
            true,
            false,
            false,
            fn (AMQPMessage $message) => $callback($message)
        );

        while ($channel->is_consuming()) {
            $channel->wait();
        }
        $channel->close();
        $this->connection->close();
    }
}
