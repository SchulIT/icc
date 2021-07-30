<?php

namespace App\Tests\Entity;

use App\Entity\Gender;
use App\Entity\Student;
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

        $student->setStatus('active');
        $this->assertEquals('active', $student->getStatus());
        $student->setStatus(null);
        $this->assertNull($student->getStatus());

        $gender = Gender::X();
        $student->setGender($gender);
        $this->assertEquals($gender, $student->getGender());
    }
}