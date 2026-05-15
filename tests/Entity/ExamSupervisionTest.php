<?php

namespace App\Tests\Entity;

use App\Exam\Entity\Exam;
use App\Exam\Entity\ExamSupervision;
use App\Common\Entity\Teacher;
use PHPUnit\Framework\TestCase;

class ExamSupervisionTest extends TestCase {
    public function testGettersSetters() {
        $invigilator = new ExamSupervision();

        $this->assertNull($invigilator->getId());

        $teacher = new Teacher();
        $invigilator->setTeacher($teacher);
        $this->assertEquals($teacher, $invigilator->getTeacher());

        $exam = new Exam();
        $invigilator->setExam($exam);
        $this->assertEquals($exam, $invigilator->getExam());

        $invigilator->setLesson(1);
        $this->assertEquals(1, $invigilator->getLesson());
    }
}