<?php

namespace App\Tests\Entity;

use App\Entity\Message;
use App\Entity\MessageFile;
use PHPUnit\Framework\TestCase;

class MessageFileTest extends TestCase {
    public function testGettersSetters() {
        $file = new MessageFile();

        $this->assertNull($file->getId());

        $file->setExtensions([]);
        $this->assertEquals([], $file->getExtensions());

        $file->setExtensions(['png']);
        $this->assertEquals(['png'], $file->getExtensions());

        $file->setLabel('label');
        $this->assertEquals('label', $file->getLabel());

        $message = new Message();
        $file->setMessage($message);
        $this->assertEquals($message, $file->getMessage());
    }
}