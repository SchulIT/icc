<?php

namespace App\Tests\Entity;

use App\Entity\Exam;
use App\Entity\ExamInvigilator;
use App\Entity\Teacher;
use PHPUnit\Framework\TestCase;

class ExamInvigilatorTest extends TestCase {
    public function testGettersSetters() {
        $invigilator = new ExamInvigilator();

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