<?php

namespace App\Tests\Security;

use App\Common\Entity\Student;
use App\Common\Entity\Teacher;
use App\Common\Entity\User;
use App\Common\Entity\UserType;
use App\Common\Security\InvalidAccountException;
use App\Common\Security\UserChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;

class UserCheckerTest extends TestCase {

    public function testValidTeacher() {
        $user = (new User())
            ->setTeacher(new Teacher())
            ->setUserType(UserType::Teacher);

        $checker = new UserChecker();
        $checker->checkPostAuth($user);

        $this->assertTrue(true);
    }

    public function testInvalidTeacher() {
        $this->expectException(InvalidAccountException::class);
        $user = (new User())
            ->setTeacher(null)
            ->setUserType(UserType::Teacher);

        $checker = new UserChecker();
        $checker->checkPostAuth($user);
    }

    public function testValidStudent() {
        $user = (new User())
            ->setUserType(UserType::Student);
        $user->addStudent(new Student());

        $checker = new UserChecker();
        $checker->checkPostAuth($user);

        $this->assertTrue(true);
    }

    public function testInvalidStudentEmptyStudents() {
        $this->expectException(InvalidAccountException::class);
        $user = (new User())
            ->setUserType(UserType::Student);

        $checker = new UserChecker();
        $checker->checkPostAuth($user);
    }

    public function testInvalidStudentTwoStudents() {
        $this->expectException(InvalidAccountException::class);
        $user = (new User())
            ->setUserType(UserType::Student);
        $user->addStudent(new Student());
        $user->addStudent(new Student());

        $checker = new UserChecker();
        $checker->checkPostAuth($user);
    }

    public function testValidParent() {
        $user = (new User())
            ->setUserType(UserType::Parent);
        $user->addStudent(new Student());

        $checker = new UserChecker();
        $checker->checkPostAuth($user);

        $this->assertTrue(true);
    }

    public function testInvalidParent() {
        $this->expectException(InvalidAccountException::class);
        $user = (new User())
            ->setUserType(UserType::Parent);

        $checker = new UserChecker();
        $checker->checkPostAuth($user);
    }
}