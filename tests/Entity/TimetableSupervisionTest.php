<?php

namespace App\Tests\Entity;

use App\Entity\Teacher;
use App\Entity\TimetableSupervision;
use DateTime;
use PHPUnit\Framework\TestCase;

class TimetableSupervisionTest extends TestCase {
    public function testGettersSetters() {
        $supervision = new TimetableSupervision();

        $this->assertNull($supervision->getId());

        $teacher = new Teacher();
        $supervision->setTeacher($teacher);
        $this->assertEquals($teacher, $supervision->getTeacher());

        $supervision->setLesson(1);
        $this->assertEquals(1, $supervision->getLesson());

        $date = new DateTime('2022-07-01');
        $supervision->setDate($date);
        $this->assertEquals($date, $supervision->getDate());

        $supervision->setLocation('location');
        $this->assertEquals('location', $supervision->getLocation());

        $supervision->setIsBefore(true);
        $this->assertTrue($supervision->isBefore());

        $supervision->setIsBefore(false);
        $this->assertFalse($supervision->isBefore());
    }
}