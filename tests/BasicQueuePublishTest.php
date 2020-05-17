<?php

declare(strict_types=1);

namespace Tests;

use AmqpBasic\BasicQueuePublish;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;

final class BasicQueuePublishTest extends TestCase
{
    public function testPublishes(): void
    {
        $channel = $this->createMock(AMQPChannel::class);
        $channel->expects($this->once())->method('queue_declare');
        $channel->expects($this->once())->method('basic_publish')->with($this->equalTo(new AMQPMessage('testing')));
        $connection = $this->createMock(AMQPStreamConnection::class);
        $connection->method('channel')->willReturn($channel);
        $basicPublish = new BasicQueuePublish($connection, 'testQueue');
        $basicPublish->publish('testing');
    }
}
