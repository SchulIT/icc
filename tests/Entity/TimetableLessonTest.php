<?php

namespace App\Tests\Entity;

use App\Entity\Room;
use App\Entity\TimetableLesson;
use App\Entity\TimetablePeriod;
use App\Entity\TimetableWeek;
use App\Entity\Tuition;
use PHPUnit\Framework\TestCase;

class TimetableLessonTest extends TestCase {
    public function testGettersSetters() {
        $lesson = new TimetableLesson();

        $this->assertNull($lesson->getId());

        $lesson->setDay(1);
        $this->assertEquals(1, $lesson->getDay());

        $lesson->setLesson(2);
        $this->assertEquals(2, $lesson->getLesson());

        $room = new Room();
        $lesson->setRoom($room);
        $this->assertEquals($room, $lesson->getRoom());

        $week = new TimetableWeek();
        $lesson->setWeek($week);
        $this->assertEquals($week, $lesson->getWeek());

        $period = new TimetablePeriod();
        $lesson->setPeriod($period);
        $this->assertEquals($period, $lesson->getPeriod());

        $tuition = new Tuition();
        $lesson->setTuition($tuition);
        $this->assertEquals($tuition, $lesson->getTuition());
    }
}