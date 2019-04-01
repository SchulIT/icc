<?php

namespace App\Tests\Entity;

use App\Entity\Message;
use App\Entity\MessageAttachment;
use PHPUnit\Framework\TestCase;

class MessageAttachmentTest extends TestCase {
    public function testGettersSetters() {
        $attachment = new MessageAttachment();

        $this->assertNull($attachment->getId());

        $attachment->setFilename('file.txt');
        $this->assertEquals('file.txt', $attachment->getFilename());

        $message = new Message();
        $attachment->setMessage($message);
        $this->assertEquals($message, $attachment->getMessage());
    }
}