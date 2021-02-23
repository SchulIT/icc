<?php

namespace App\Tests\Entity;

use App\Entity\TimetableWeek;
use PHPUnit\Framework\TestCase;

class TimetableWeekTest extends TestCase {
    public function testGettersSetters() {
        $week = new TimetableWeek();

        $this->assertNull($week->getId());

        $week->setKey('key');
        $this->assertEquals('key', $week->getKey());

        $week->setDisplayName('display-name');
        $this->assertEquals('display-name', $week->getDisplayName());
    }
}