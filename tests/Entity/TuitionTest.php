<?php

namespace App\Tests\Entity;

use App\Entity\StudyGroup;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\Tuition;
use PHPUnit\Framework\TestCase;

class TuitionTest extends TestCase {
    public function testGettersSetters() {
        $tuition = new Tuition();

        $this->assertNull($tuition->getId());

        $tuition->setExternalId('external-id');
        $this->assertEquals('external-id', $tuition->getExternalId());

        $tuition->setName('name');
        $this->assertEquals('name', $tuition->getName());

        $subject = new Subject();
        $tuition->setSubject($subject);
        $this->assertEquals($subject, $tuition->getSubject());

        $teacher = new Teacher();
        $tuition->setTeacher($teacher);
        $this->assertEquals($teacher, $tuition->getTeacher());

        $studyGroup = new StudyGroup();
        $tuition->setStudyGroup($studyGroup);
        $this->assertEquals($studyGroup, $tuition->getStudyGroup());

        $teacher = new Teacher();
        $tuition->addAdditionalTeacher($teacher);
        $this->assertTrue($tuition->getAdditionalTeachers()->contains($teacher));

        $tuition->removeAdditionalTeacher($teacher);
        $this->assertFalse($tuition->getAdditionalTeachers()->contains($teacher));
    }
}