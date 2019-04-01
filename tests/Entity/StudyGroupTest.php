<?php

namespace App\Tests\Entity;

use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupType;
use PHPUnit\Framework\TestCase;

class StudyGroupTest extends TestCase {
    public function testGettersSetters() {
        $studyGroup = new StudyGroup();

        $this->assertNull($studyGroup->getId());

        $studyGroup->setExternalId('external-id');
        $this->assertEquals('external-id', $studyGroup->getExternalId());

        $studyGroup->setName('name');
        $this->assertEquals('name', $studyGroup->getName());

        $type = StudyGroupType::Grade();
        $studyGroup->setType($type);
        $this->assertEquals($type, $studyGroup->getType());

        $grade = new Grade();
        $studyGroup->addGrade($grade);
        $this->assertTrue($studyGroup->getGrades()->contains($grade));

        $studyGroup->removeGrade($grade);
        $this->assertFalse($studyGroup->getGrades()->contains($grade));

        $student = new Student();
        $studyGroup->addStudent($student);
        $this->assertTrue($studyGroup->getStudents()->contains($student));

        $studyGroup->removeStudent($student);
        $this->assertFalse($studyGroup->getStudents()->contains($student));
    }
}