<?php

namespace App\Tests\Entity;

use App\Entity\Grade;
use PHPUnit\Framework\TestCase;

class GradeTest extends TestCase {
    public function testGettersSetters() {
        $grade = new Grade();

        $this->assertNull($grade->getId());

        $grade->setName('name');
        $this->assertEquals('name', $grade->getName());
    }
}