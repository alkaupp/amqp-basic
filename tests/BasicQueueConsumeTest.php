<?php

declare(strict_types=1);

namespace Tests;

use AmqpBasic\BasicQueueConsume;
use PhpAmqpLib\Channel\AbstractChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;

final class BasicQueueConsumeTest extends TestCase
{
    public function testConsumes(): void
    {
        $connection = $this->createMock(AMQPStreamConnection::class);
        $connection->method('channel')->willReturn($this->createChannel($connection));
        $basicConsume = new BasicQueueConsume($connection, 'testqueue');
        $obj = new \stdClass();
        $basicConsume->consume(fn (AMQPMessage $message) => $obj->body = $message->body);
        $this->assertSame('test', $obj->body);
    }

    private function createChannel(AMQPStreamConnection $connection)
    {
        return new class($connection) extends AbstractChannel {
            public function __construct(AbstractConnection $connection)
            {
                parent::__construct($connection, 0);
            }

            private $callback;

            public function queue_declare($queue, $passive, $durable, $exclusive, $auto_delete)
            {
            }

            public function basic_consume(
                $queue = '',
                $consumer_tag = '',
                $no_local = false,
                $no_ack = false,
                $exclusive = false,
                $nowait = false,
                $callback = null,
                $ticket = null,
                $arguments = []
            ) {
                $this->callback = $callback;
                return $consumer_tag;
            }

            public function is_consuming()
            {
                return $this->callback !== null;
            }

            public function wait($allowed_methods = null, $non_blocking = false, $timeout = 0)
            {
                $callback = $this->callback;
                $result = $callback(new AMQPMessage('test'));
                $this->callback = null;
                return $result;
            }

            public function close(): void
            {
            }
        };
    }
}
