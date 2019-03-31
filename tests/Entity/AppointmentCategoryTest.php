<?php

namespace App\Tests\Entity;

use App\Entity\AppointmentCategory;
use PHPUnit\Framework\TestCase;

class AppointmentCategoryTest extends TestCase {
    public function testGettersSetters() {
        $category = new AppointmentCategory();

        $this->assertNull($category->getId());

        $category->setExternalId('external-id');
        $this->assertEquals('external-id', $category->getExternalId());

        $category->setName('name');
        $this->assertEquals('name', $category->getName());

        $category->setColor('#color');
        $this->assertEquals('#color', $category->getColor());

        $category->setColor(null);
        $this->assertNull($category->getColor());
    }
}