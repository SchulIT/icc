<?php

namespace App\Entity;

use PHPUnit\Framework\TestCase;

class DocumentVisibilityTest extends TestCase {
    public function testGettersSetters() {
        $visibility = new DocumentVisibility();

        $document = new Document();
        $visibility->setDocument($document);
        $this->assertEquals($document, $visibility->getDocument());

        $type = UserType::Teacher();
        $visibility->setUserType($type);
        $this->assertEquals($type, $visibility->getUserType());
    }
}