<?php

namespace App\Tests\Entity;

use App\Entity\Gender;
use App\Entity\Grade;
use App\Entity\Message;
use App\Entity\Student;
use App\Entity\StudentStatus;
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
        $user->setStudent($student);
        $this->assertEquals($student, $user->getStudent());
        $this->assertEquals(1, count($user->getStudents()));
        $this->assertEquals($student, $user->getStudents()[0]);

        $teacher = new Teacher();
        $user->setTeacher($teacher);
        $this->assertEquals($teacher, $user->getTeacher());

        $anotherUser = new User();
        $user->addLinkedUser($anotherUser);
        $this->assertTrue($user->getLinkedUsers()->contains($anotherUser));

        $user->removeLinkedUser($anotherUser);
        $this->assertFalse($user->getLinkedUsers()->contains($anotherUser));

        $message = new Message();
        $user->addDismissedMessage($message);
        $this->assertTrue($user->getDismissedMessages()->contains($message));

        $user->removeDismissedMessage($message);
        $this->assertFalse($user->getDismissedMessages()->contains($message));
    }

    public function testLinkingUsers() {
        $kernel = static::createKernel();
        $kernel->boot();

        $em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $gradeEF = (new Grade())
            ->setName('EF');
        $grade8A = (new Grade())
            ->setName('8A');

        $userEF = (new User())
            ->setFirstname('firstname')
            ->setLastname('lastname')
            ->setUsername('username2')
            ->setEmail('username2@school.it')
            ->setUserType(UserType::Student())
            ->setStudent(
                (new Student())
                    ->setLastname('lastname')
                    ->setFirstname('firstname')
                    ->setExternalId('student-ef')
                    ->setStatus(StudentStatus::Active())
                    ->setGender(Gender::X())
                    ->setIsFullAged(true)
                    ->setGrade($gradeEF)
            );

        $this->assertTrue(in_array($gradeEF, $userEF->getGrades()));

        $user8A = (new User())
            ->setFirstname('firstname')
            ->setLastname('lastname')
            ->setUsername('username')
            ->setEmail('username@school.it')
            ->setUserType(UserType::Student())
            ->setStudent(
                (new Student())
                    ->setLastname('lastname')
                    ->setFirstname('firstname')
                    ->setExternalId('student-8a')
                    ->setStatus(StudentStatus::Active())
                    ->setGender(Gender::X())
                    ->setIsFullAged(true)
                    ->setGrade($grade8A)
            );

        $userEF->addLinkedUser($user8A);

        $em->persist($grade8A);
        $em->persist($gradeEF);
        $em->persist($user8A->getStudent());
        $em->persist($userEF->getStudent());
        $em->persist($user8A);
        $em->persist($userEF);
        $em->flush();

        $this->assertTrue(in_array($gradeEF, $userEF->getGrades()));
        $this->assertTrue(in_array($grade8A, $userEF->getGrades()));

        $em->detach($user8A);
        $em->detach($userEF);

        /** @var User $user8A */
        $user8A = $em->getRepository(User::class)
            ->findOneBy([
                'id' => $user8A->getId()
            ]);

        $this->assertEquals(1, $user8A->getLinkingUsers()->count());
        $this->assertEquals($userEF->getId(), $user8A->getLinkingUsers()->first()->getId());
    }

}