<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Gender;
use App\Entity\Grade;
use App\Entity\GradeMembership;
use App\Entity\Section;
use App\Entity\Setting;
use App\Entity\Student;
use App\Entity\User;
use App\Entity\UserType;
use DateTime;
use Faker\Generator;
use Ramsey\Uuid\Uuid;

class SickNoteControllerTest extends AbstractControllerTest {

    private $em;

    private $parent;

    private $studentUser;

    private $nonFullAgedStudentUser;

    private $fullAgedStudent;

    private $nonFullAgedStudent;

    private $viewerUser;

    private $creatorUser;

    public function setUp(): void {
        $this->client = static::createClient();
        $this->em = $this->client->getContainer()->get('doctrine')->getManager();

        /** @var Generator $faker */
        $faker = $this->client->getContainer()->get(Generator::class);

        $section = (new Section())
            ->setDisplayName('Testabschnitt')
            ->setStart((new DateTime())->modify('-30 days'))
            ->setEnd((new DateTime())->modify('+30 days'))
            ->setYear(2021)
            ->setNumber(1);
        $this->em->persist($section);

        $grade = (new Grade())
            ->setName('EF')
            ->setExternalId('EF');

        $this->em->persist($grade);

        $this->fullAgedStudent = (new Student())
            ->setUniqueIdentifier(md5(uniqid()))
            ->setFirstname($faker->firstName)
            ->setLastname($faker->lastName)
            ->setExternalId('TEST1')
            ->setEmail($faker->email)
            ->setGender(Gender::X())
            ->setBirthday(new DateTime('1990-01-01'));
        $this->fullAgedStudent->addSection($section);
        $this->fullAgedStudent->addGradeMembership(
            (new GradeMembership())
                ->setStudent($this->fullAgedStudent)
                ->setGrade($grade)
                ->setSection($section)
        );

        $this->nonFullAgedStudent = (new Student())
            ->setUniqueIdentifier(md5(uniqid()))
            ->setFirstname($faker->firstName)
            ->setLastname($faker->lastName)
            ->setExternalId('TEST2')
            ->setEmail($faker->email)
            ->setGender(Gender::X())
            ->setBirthday((new DateTime())->modify('-10 year'));
        $this->nonFullAgedStudent->addSection($section);
        $this->nonFullAgedStudent->addGradeMembership(
            (new GradeMembership())
                ->setStudent($this->nonFullAgedStudent)
                ->setGrade($grade)
                ->setSection($section)
        );

        $this->parent = (new User())
            ->setUserType(UserType::Parent())
            ->setEmail($faker->email)
            ->setUsername($faker->email)
            ->setIdpId(Uuid::fromString($faker->uuid));
        $this->parent->setRoles(['ROLE_USER']);
        $this->parent->addStudent($this->nonFullAgedStudent);

        $this->studentUser = (new User())
            ->setUserType(UserType::Student())
            ->setEmail($faker->email)
            ->setUsername($faker->email)
            ->setIdpId(Uuid::fromString($faker->uuid));
        $this->studentUser->setRoles(['ROLE_USER']);
        $this->studentUser->addStudent($this->fullAgedStudent);

        $this->nonFullAgedStudentUser = (new User())
            ->setUserType(UserType::Student())
            ->setEmail($faker->email)
            ->setUsername($faker->email)
            ->setIdpId(Uuid::fromString($faker->uuid));
        $this->nonFullAgedStudentUser->setRoles(['ROLE_USER']);
        $this->nonFullAgedStudentUser->addStudent($this->nonFullAgedStudent);

        $this->viewerUser = (new User())
            ->setUserType(UserType::User())
            ->setEmail($faker->email)
            ->setUsername($faker->email)
            ->setIdpId(Uuid::fromString($faker->uuid));
        $this->viewerUser->setRoles(['ROLE_USER', 'ROLE_STUDENT_ABSENCE_VIEWER']);

        $this->creatorUser = (new User())
            ->setUserType(UserType::User())
            ->setEmail($faker->email)
            ->setUsername($faker->email)
            ->setIdpId(Uuid::fromString($faker->uuid));
        $this->creatorUser->setRoles(['ROLE_USER', 'ROLE_STUDENT_ABSENCE_CREATOR']);

        $this->em->persist($this->nonFullAgedStudent);
        $this->em->persist($this->fullAgedStudent);
        $this->em->persist($this->parent);
        $this->em->persist($this->studentUser);
        $this->em->persist($this->nonFullAgedStudentUser);
        $this->em->persist($this->viewerUser);
        $this->em->persist($this->creatorUser);

        $this->em->persist((new Setting())->setKey('student_absences.enabled')->setValue(true));

        $this->em->flush();
    }

    public function testParentCanCreateSickNote() {
        $this->login($this->parent, $this->client->getKernel());
        $this->client->request('GET', '/absences/add');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testNonFullAgedStudentCannotCreateSickNote() {
        $this->login($this->nonFullAgedStudentUser, $this->client->getKernel());
        $this->client->request('GET', '/absences/add');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testFullAgedStudentCanCreateSickNote() {
        $this->login($this->studentUser, $this->client->getKernel());
        $this->client->request('GET', '/absences/add');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testUserWithRoleCanCreateSickNote() {
        $this->login($this->creatorUser, $this->client->getKernel());
        $this->client->request('GET', '/absences/add');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testUserWithRoleCanViewSickNotes() {
        $this->login($this->viewerUser, $this->client->getKernel());
        $this->client->request('GET', '/absences');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testParentCanViewSickNotes() {
        $this->login($this->parent, $this->client->getKernel());
        $this->client->request('GET', '/absences');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testNonFullAgedStudentCannotViewSickNotes() {
        $this->login($this->nonFullAgedStudentUser, $this->client->getKernel());
        $this->client->request('GET', '/absences');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testFullAgedStudentCanViewSickNotes() {
        $this->login($this->studentUser, $this->client->getKernel());
        $this->client->request('GET', '/absences');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}