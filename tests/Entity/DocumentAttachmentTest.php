<?php

namespace App\Tests\Entity;

use App\Entity\Document;
use App\Entity\DocumentAttachment;
use PHPUnit\Framework\TestCase;

class DocumentAttachmentTest extends TestCase {
    public function testGettersSetters() {
        $attachment = new DocumentAttachment();

        $this->assertNull($attachment->getId());

        $document = new Document();
        $attachment->setDocument($document);
        $this->assertEquals($document, $attachment->getDocument());

        $attachment->setFilename('file.txt');
        $this->assertEquals('file.txt', $attachment->getFilename());
    }
}