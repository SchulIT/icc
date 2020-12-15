<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Gender;
use App\Entity\Grade;
use App\Entity\Setting;
use App\Entity\Student;
use App\Entity\User;
use App\Entity\UserType;
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

    public function setUp() {
        $this->client = static::createClient();
        $this->em = $this->client->getContainer()->get('doctrine')->getManager();

        /** @var Generator $faker */
        $faker = $this->client->getContainer()->get(Generator::class);

        $grade = (new Grade())
            ->setName('EF')
            ->setExternalId('EF');

        $this->em->persist($grade);

        $this->fullAgedStudent = (new Student())
            ->setFirstname($faker->firstName)
            ->setLastname($faker->lastName)
            ->setExternalId('TEST1')
            ->setEmail($faker->email)
            ->setGender(Gender::X())
            ->setGrade($grade)
            ->setIsFullAged(true);

        $this->nonFullAgedStudent = (new Student())
            ->setFirstname($faker->firstName)
            ->setLastname($faker->lastName)
            ->setExternalId('TEST2')
            ->setEmail($faker->email)
            ->setGender(Gender::X())
            ->setGrade($grade)
            ->setIsFullAged(false);

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
        $this->viewerUser->setRoles(['ROLE_USER', 'ROLE_SICK_NOTE_VIEWER']);

        $this->creatorUser = (new User())
            ->setUserType(UserType::User())
            ->setEmail($faker->email)
            ->setUsername($faker->email)
            ->setIdpId(Uuid::fromString($faker->uuid));
        $this->creatorUser->setRoles(['ROLE_USER', 'ROLE_SICK_NOTE_CREATOR']);

        $this->em->persist($this->nonFullAgedStudent);
        $this->em->persist($this->fullAgedStudent);
        $this->em->persist($this->parent);
        $this->em->persist($this->studentUser);
        $this->em->persist($this->nonFullAgedStudentUser);
        $this->em->persist($this->viewerUser);
        $this->em->persist($this->creatorUser);

        $this->em->persist((new Setting())->setKey('sick_note.enabled')->setValue(true));

        $this->em->flush();
    }

    public function testParentCanCreateSickNote() {
        $this->login($this->parent, $this->client->getKernel());
        $this->client->request('GET', '/sick_note');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testNonFullAgedStudentCannotCreateSickNote() {
        $this->login($this->nonFullAgedStudentUser, $this->client->getKernel());
        $this->client->request('GET', '/sick_note');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testFullAgedStudentCanCreateSickNote() {
        $this->login($this->studentUser, $this->client->getKernel());
        $this->client->request('GET', '/sick_note');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testUserWithRoleCanCreateSickNote() {
        $this->login($this->creatorUser, $this->client->getKernel());
        $this->client->request('GET', '/sick_note');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testUserWithRoleCanViewSickNotes() {
        $this->login($this->viewerUser, $this->client->getKernel());
        $this->client->request('GET', '/sick_notes');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testUserWithoutRoleCannotViewSickNotes() {
        $this->login($this->creatorUser, $this->client->getKernel());
        $this->client->request('GET', '/sick_notes');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testParentCannotViewSickNotes() {
        $this->login($this->parent, $this->client->getKernel());
        $this->client->request('GET', '/sick_notes');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testNonFullAgedStudentCannotViewSickNotes() {
        $this->login($this->nonFullAgedStudentUser, $this->client->getKernel());
        $this->client->request('GET', '/sick_notes');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testFullAgedStudentCannotViewSickNotes() {
        $this->login($this->studentUser, $this->client->getKernel());
        $this->client->request('GET', '/sick_notes');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }
}