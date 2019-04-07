<?php

namespace App\Tests\Entity;

use App\Entity\Subject;
use App\Entity\Teacher;
use PHPUnit\Framework\TestCase;

class SubjectTest extends TestCase {
    public function testGettersSetters() {
        $subject = new Subject();

        $this->assertNull($subject->getId());

        $subject->setAbbreviation('abbr');
        $this->assertEquals('abbr', $subject->getAbbreviation());

        $subject->setName('name');
        $this->assertEquals('name', $subject->getName());

        $subject->setColor('color');
        $this->assertEquals('color', $subject->getColor());

        $teacher = new Teacher();
        $subject->setDepartmentChairman($teacher);
        $this->assertEquals($teacher, $subject->getDepartmentChairman());

        $subject->setIsVisibleCourses(true);
        $this->assertTrue($subject->isVisibleCourses());

        $subject->setIsVisibleCourses(false);
        $this->assertFalse($subject->isVisibleCourses());

        $subject->setIsVisibleGrades(true);
        $this->assertTrue($subject->isVisibleGrades());

        $subject->setIsVisibleGrades(false);
        $this->assertFalse($subject->isVisibleGrades());

        $subject->setIsVisibleRooms(true);
        $this->assertTrue($subject->isVisibleRooms());

        $subject->setIsVisibleRooms(false);
        $this->assertFalse($subject->isVisibleRooms());

        $subject->setIsVisibleStudents(true);
        $this->assertTrue($subject->isVisibleStudents());

        $subject->setIsVisibleStudents(false);
        $this->assertFalse($subject->isVisibleStudents());

        $subject->setIsVisibleSubjects(true);
        $this->assertTrue($subject->isVisibleSubjects());

        $subject->setIsVisibleSubjects(false);
        $this->assertFalse($subject->isVisibleSubjects());

        $subject->setIsVisibleTeachers(true);
        $this->assertTrue($subject->isVisibleTeachers());

        $subject->setIsVisibleTeachers(false);
        $this->assertFalse($subject->isVisibleTeachers());

        $subject->setReplaceSubjectAbbreviation(true);
        $this->assertTrue($subject->isReplaceSubjectAbbreviation());

        $subject->setReplaceSubjectAbbreviation(false);
        $this->assertFalse($subject->isReplaceSubjectAbbreviation());
    }
}