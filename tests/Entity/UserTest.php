<?php

namespace App\Tests\Entity;

use App\Entity\Message;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use App\Entity\UserType;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserTest extends WebTestCase {
    public function testGettersSetters() {
        $user = new User();

        $this->assertNull($user->getId());

        $user->setUsername('username');
        $this->assertEquals('username', $user->getUsername());

        $user->setFirstname('firstname');
        $this->assertEquals('firstname', $user->getFirstname());

        $user->setLastname('lastname');
        $this->assertEquals('lastname', $user->getLastname());

        $user->setEmail('username@school.it');
        $this->assertEquals('username@school.it', $user->getEmail());

        $type = UserType::Student();
        $user->setUserType($type);
        $this->assertEquals($type, $user->getUserType());

        $user->setRoles(['ROLE_TEST']);
        $this->assertEquals(['ROLE_TEST'], $user->getRoles());

        $student = new Student();
        $user->addStudent($student);
        $this->assertEquals($student, $user->getStudents()->first());
        $this->assertEquals(1, count($user->getStudents()));

        $teacher = new Teacher();
        $user->setTeacher($teacher);
        $this->assertEquals($teacher, $user->getTeacher());

        $message = new Message();
        $user->addDismissedMessage($message);
        $this->assertTrue($user->getDismissedMessages()->contains($message));

        $user->removeDismissedMessage($message);
        $this->assertFalse($user->getDismissedMessages()->contains($message));
    }
}