<?php

namespace App\Tests\Security;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use App\Entity\UserType;
use App\Security\UserChecker;
use PHPUnit\Framework\TestCase;

class UserCheckerTest extends TestCase {

    public function testValidTeacher() {
        $user = (new User())
            ->setTeacher(new Teacher())
            ->setUserType(UserType::Teacher());

        $checker = new UserChecker();
        $checker->checkPostAuth($user);

        $this->assertTrue(true);
    }

    /**
     * @expectedException App\Security\InvalidAccountException
     */
    public function testInvalidTeacher() {
        $user = (new User())
            ->setTeacher(null)
            ->setUserType(UserType::Teacher());

        $checker = new UserChecker();
        $checker->checkPostAuth($user);
    }

    public function testValidStudent() {
        $user = (new User())
            ->setUserType(UserType::Student());
        $user->addStudent(new Student());

        $checker = new UserChecker();
        $checker->checkPostAuth($user);

        $this->assertTrue(true);
    }

    /**
     * @expectedException App\Security\InvalidAccountException
     */
    public function testInvalidStudentEmptyStudents() {
        $user = (new User())
            ->setUserType(UserType::Student());

        $checker = new UserChecker();
        $checker->checkPostAuth($user);
    }

    /**
     * @expectedException App\Security\InvalidAccountException
     */
    public function testInvalidStudentTwoStudents() {
        $user = (new User())
            ->setUserType(UserType::Student());
        $user->addStudent(new Student());
        $user->addStudent(new Student());

        $checker = new UserChecker();
        $checker->checkPostAuth($user);
    }

    public function testValidParent() {
        $user = (new User())
            ->setUserType(UserType::Parent());
        $user->addStudent(new Student());

        $checker = new UserChecker();
        $checker->checkPostAuth($user);

        $this->assertTrue(true);
    }

    /**
     * @expectedException App\Security\InvalidAccountException
     */
    public function testInvalidParent() {
        $user = (new User())
            ->setUserType(UserType::Parent());

        $checker = new UserChecker();
        $checker->checkPostAuth($user);
    }
}