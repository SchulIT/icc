<?php

namespace App\Tests\Entity;

use App\Entity\Teacher;
use App\Entity\TimetablePeriod;
use App\Entity\TimetableSupervision;
use App\Entity\TimetableWeek;
use PHPUnit\Framework\TestCase;

class TimetableSupervisionTest extends TestCase {
    public function testGettersSetters() {
        $supervision = new TimetableSupervision();

        $this->assertNull($supervision->getId());

        $teacher = new Teacher();
        $supervision->setTeacher($teacher);
        $this->assertEquals($teacher, $supervision->getTeacher());

        $period = new TimetablePeriod();
        $supervision->setPeriod($period);
        $this->assertEquals($period, $supervision->getPeriod());

        $supervision->setLesson(1);
        $this->assertEquals(1, $supervision->getLesson());

        $supervision->setDay(2);
        $this->assertEquals(2, $supervision->getDay());

        $supervision->setLocation('location');
        $this->assertEquals('location', $supervision->getLocation());

        $supervision->setIsBefore(true);
        $this->assertTrue($supervision->isBefore());

        $supervision->setIsBefore(false);
        $this->assertFalse($supervision->isBefore());
    }
}