<?php

namespace App\Tests\Security;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use App\Entity\UserType;
use App\Security\InvalidAccountException;
use App\Security\UserChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;

class UserCheckerTest extends TestCase {

    private function getEvent($token) {
        return new AuthenticationSuccessEvent($token);
    }

    private function getToken(?User $user) {
        $mock = $this->createMock(TokenInterface::class);
        $mock->method('getUser')->willReturn($user);

        return $mock;
    }

    public function testValidTeacher() {
        $user = (new User())
            ->setTeacher(new Teacher())
            ->setUserType(UserType::Teacher);

        $checker = new UserChecker();
        $checker->onAuthenticationSuccess($this->getEvent($this->getToken($user)));

        $this->assertTrue(true);
    }

    public function testInvalidTeacher() {
        $this->expectException(InvalidAccountException::class);
        $user = (new User())
            ->setTeacher(null)
            ->setUserType(UserType::Teacher);

        $checker = new UserChecker();
        $checker->onAuthenticationSuccess($this->getEvent($this->getToken($user)));
    }

    public function testValidStudent() {
        $user = (new User())
            ->setUserType(UserType::Student);
        $user->addStudent(new Student());

        $checker = new UserChecker();
        $checker->onAuthenticationSuccess($this->getEvent($this->getToken($user)));

        $this->assertTrue(true);
    }

    public function testInvalidStudentEmptyStudents() {
        $this->expectException(InvalidAccountException::class);
        $user = (new User())
            ->setUserType(UserType::Student);

        $checker = new UserChecker();
        $checker->onAuthenticationSuccess($this->getEvent($this->getToken($user)));
    }

    public function testInvalidStudentTwoStudents() {
        $this->expectException(InvalidAccountException::class);
        $user = (new User())
            ->setUserType(UserType::Student);
        $user->addStudent(new Student());
        $user->addStudent(new Student());

        $checker = new UserChecker();
        $checker->onAuthenticationSuccess($this->getEvent($this->getToken($user)));
    }

    public function testValidParent() {
        $user = (new User())
            ->setUserType(UserType::Parent);
        $user->addStudent(new Student());

        $checker = new UserChecker();
        $checker->onAuthenticationSuccess($this->getEvent($this->getToken($user)));

        $this->assertTrue(true);
    }

    public function testInvalidParent() {
        $this->expectException(InvalidAccountException::class);
        $user = (new User())
            ->setUserType(UserType::Parent);

        $checker = new UserChecker();
        $checker->onAuthenticationSuccess($this->getEvent($this->getToken($user)));
    }
}