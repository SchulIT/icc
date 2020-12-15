<?php

namespace App\Tests\Entity;

use App\Entity\Exam;
use App\Entity\ExamSupervision;
use App\Entity\Room;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Tuition;
use PHPUnit\Framework\TestCase;

class ExamTest extends TestCase {
    public function testGettersSetters() {
        $exam = new Exam();

        $this->assertNull($exam->getId());

        $exam->setExternalId('external-id');
        $this->assertEquals('external-id', $exam->getExternalId());

        $date = new \DateTime('2019-02-01');
        $exam->setDate($date);
        $this->assertEquals($date, $exam->getDate());

        $exam->setLessonStart(1);
        $this->assertEquals(1, $exam->getLessonStart());

        $exam->setLessonEnd(3);
        $this->assertEquals(3, $exam->getLessonEnd());

        $exam->setDescription('description');
        $this->assertEquals('description', $exam->getDescription());

        $exam->setDescription(null);
        $this->assertNull($exam->getDescription());

        $room = new Room();
        $exam->setRoom($room);
        $this->assertEquals($room, $exam->getRoom());

        $tuition = new Tuition();
        $exam->addTuition($tuition);
        $this->assertTrue($exam->getTuitions()->contains($tuition));

        $exam->removeTuition($tuition);
        $this->assertFalse($exam->getTuitions()->contains($tuition));

        $student = new Student();
        $exam->addStudent($student);
        $this->assertTrue($exam->getStudents()->contains($student));

        $exam->removeStudent($student);
        $this->assertFalse($exam->getStudents()->contains($student));

        $invigilator = new ExamSupervision();
        $exam->addSupervision($invigilator);
        $this->assertTrue($exam->getSupervisions()->contains($invigilator));

        $exam->removeSupervision($invigilator);
        $this->assertFalse($exam->getSupervisions()->contains($invigilator));
    }
}