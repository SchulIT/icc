<?php

namespace App\Tests\Entity;

use App\Entity\Room;
use App\Entity\TimetableLesson;
use App\Entity\TimetableWeek;
use App\Entity\Tuition;
use DateTime;
use PHPUnit\Framework\TestCase;

class TimetableLessonTest extends TestCase {
    public function testGettersSetters() {
        $lesson = new TimetableLesson();

        $this->assertNull($lesson->getId());

        $date = new DateTime('2022-07-01');
        $lesson->setDate($date);
        $this->assertEquals($date, $lesson->getDate());

        $lesson->setLessonStart(1);
        $this->assertEquals(1, $lesson->getLessonStart());

        $lesson->setLessonEnd(2);
        $this->assertEquals(2, $lesson->getLessonEnd());

        $room = new Room();
        $lesson->setRoom($room);
        $this->assertEquals($room, $lesson->getRoom());

        $tuition = new Tuition();
        $lesson->setTuition($tuition);
        $this->assertEquals($tuition, $lesson->getTuition());
    }
}