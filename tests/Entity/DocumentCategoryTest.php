<?php

namespace App\Tests\Entity;

use App\Entity\DocumentCategory;
use PHPUnit\Framework\TestCase;

class DocumentCategoryTest extends TestCase {
    public function testGettersSetters() {
        $category = new DocumentCategory();

        $this->assertNull($category->getId());

        $category->setName('name');
        $this->assertEquals('name', $category->getName());
    }
}