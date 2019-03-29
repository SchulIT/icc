<?php

namespace App\Tests\Event;

use App\Entity\Message;
use App\Event\MessageCreatedEvent;
use PHPUnit\Framework\TestCase;

class MessageCreatedEventTest extends TestCase {
    public function testConstructor() {
        $message = new Message();
        $event = new MessageCreatedEvent($message);

        $this->assertEquals($message, $event->getMessage());
    }
}