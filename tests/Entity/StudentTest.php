<?php

namespace App\Tests\Entity;

use App\Entity\Gender;
use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\StudentStatus;
use PHPUnit\Framework\TestCase;

class StudentTest extends TestCase {
    public function testGettersSetters() {
        $student = new Student();

        $this->assertNull($student->getId());

        $student->setFirstname('firstname');
        $this->assertEquals('firstname', $student->getFirstname());

        $student->setLastname('lastname');
        $this->assertEquals('lastname', $student->getLastname());

        $student->setExternalId('external-id');
        $this->assertEquals('external-id', $student->getExternalId());

        $student->setIsFullAged(true);
        $this->assertTrue($student->isFullAged());

        $student->setIsFullAged(false);
        $this->assertFalse($student->isFullAged());

        $status = StudentStatus::Active();
        $student->setStatus($status);
        $this->assertEquals($status, $student->getStatus());

        $grade = new Grade();
        $student->setGrade($grade);
        $this->assertEquals($grade, $student->getGrade());

        $gender = Gender::X();
        $student->setGender($gender);
        $this->assertEquals($gender, $student->getGender());
    }
}