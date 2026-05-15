<?php

namespace App\Tests\Event;

use App\Message\Entity\Message;
use App\Message\Event\MessageCreatedEvent;
use PHPUnit\Framework\TestCase;

class MessageCreatedEventTest extends TestCase {
    public function testConstructor() {
        $message = new Message();
        $event = new MessageCreatedEvent($message, true);

        $this->assertEquals($message, $event->getMessage());
    }
}