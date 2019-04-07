<?php

namespace App\Tests\Entity;

use App\Entity\Gender;
use App\Entity\Grade;
use App\Entity\Teacher;
use PHPUnit\Framework\TestCase;

class TeacherTest extends TestCase {
    public function testGettersSetters() {
        $teacher = new Teacher();

        $this->assertNull($teacher->getId());

        $teacher->setAcronym('acronym');
        $this->assertEquals('acronym', $teacher->getAcronym());

        $teacher->setFirstname('firstname');
        $this->assertEquals('firstname', $teacher->getFirstname());

        $teacher->setLastname('lastname');
        $this->assertEquals('lastname', $teacher->getLastname());

        $gender = Gender::Female();
        $teacher->setGender($gender);
        $this->assertEquals($gender, $teacher->getGender());

        $teacher->setTitle('title');
        $this->assertEquals('title', $teacher->getTitle());

        $teacher->setTitle(null);
        $this->assertNull($teacher->getTitle());
    }
}