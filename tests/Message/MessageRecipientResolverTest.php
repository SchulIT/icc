<?php

namespace App\Tests\Message;

use App\Entity\Message;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupMembership;
use App\Entity\StudyGroupType;
use App\Entity\User;
use App\Entity\UserType;
use App\Entity\UserTypeEntity;
use App\Form\UserTypeEntityType;
use App\Message\MessageRecipientResolver;
use App\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Reflection;
use ReflectionClass;

class MessageRecipientResolverTest extends TestCase {

    private $teacherWithoutEmail;
    private $teacherUser;
    private $studentUser;
    private $student;
    private $studyGroupEF;
    private $studyGroupIFGK;

    public function setUp(): void {
        $this->teacherWithoutEmail = (new User())
            ->setUserType(UserType::Teacher());

        $this->teacherUser = (new User())
            ->setEmail('teacher@test.org')
            ->setUserType(UserType::Teacher())
            ->setIsEmailNotificationsEnabled(true);

        $reflectionClass = new ReflectionClass(StudyGroup::class);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);

        $this->studyGroupEF = (new StudyGroup())
            ->setName('EF')
            ->setType(StudyGroupType::Grade());

        $property->setValue($this->studyGroupEF, 1);

        $this->studyGroupIFGK = (new StudyGroup())
            ->setName('IF-GK1')
            ->setType(StudyGroupType::Course());

        $property->setValue($this->studyGroupIFGK, 2);

        $this->studentUser = (new User())
            ->setEmail('student@test.org')
            ->setUserType(UserType::Student())
            ->setIsEmailNotificationsEnabled(true);

        $this->student = (new Student());
        $this->student->getStudyGroupMemberships()->add(
            (new StudyGroupMembership())
            ->setType('GK')
            ->setStudyGroup($this->studyGroupIFGK)
            ->setStudent($this->student)
        );

        $this->studentUser->addStudent($this->student);
    }

    private function getUserRepository() {
        $mock = $this->createMock(UserRepositoryInterface::class);

        $mock->method('findAllByNotifyMessages')
            ->willReturn([
                $this->studentUser,
                $this->teacherUser
            ]);

        return $mock;
    }

    public function testNoMatchingUserType() {
        $visibility = (new UserTypeEntity())
            ->setUserType(UserType::User());
        $message = (new Message());
        $message->addVisibility($visibility);

        $resolver = new MessageRecipientResolver($this->getUserRepository());
        $users = $resolver->resolveRecipients($message);

        $this->assertEquals([], $users);
    }

    public function testNotReceivingForeignMessages() {
        $visibility = (new UserTypeEntity())
            ->setUserType(UserType::Teacher());
        $message = (new Message());
        $message->addVisibility($visibility);

        $resolver = new MessageRecipientResolver($this->getUserRepository());
        $users = $resolver->resolveRecipients($message);

        $this->assertEquals(1, count($users));
        $this->assertEquals($users[0], $this->teacherUser);
    }

    public function testCorrectStudyGroup() {
        $message = (new Message());
        $message->addVisibility((new UserTypeEntity())
            ->setUserType(UserType::Teacher()));
        $message->addVisibility((new UserTypeEntity())
            ->setUserType(UserType::Student()));
        $message->addStudyGroup($this->studyGroupIFGK);

        $resolver = new MessageRecipientResolver($this->getUserRepository());
        $users = $resolver->resolveRecipients($message);

        $this->assertEquals(2, count($users));
        $this->assertTrue(in_array($this->teacherUser, $users));
        $this->assertTrue(in_array($this->studentUser, $users));
    }
}