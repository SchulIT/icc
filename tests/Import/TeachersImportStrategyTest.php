<?php

namespace App\Tests\Import;

use App\Entity\Gender;
use App\Entity\Teacher;
use App\Entity\TeacherTag;
use App\Import\Importer;
use App\Import\TeachersImportStrategy;
use App\Repository\TeacherRepository;
use App\Repository\TeacherTagRepository;
use App\Request\Data\TeacherData;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TeachersImportStrategyTest extends WebTestCase {

    private $em;

    public function setUp(): void {
        $kernel = static::createKernel();
        $kernel->boot();

        $this->em = $kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->em->persist(
            (new TeacherTag())
                ->setExternalId('tag1')
                ->setName('Tag 1')
                ->setColor('#000000')
        );

        $this->em->persist(
            (new TeacherTag())
                ->setExternalId('tag2')
                ->setName('Tag 2')
                ->setColor('#111111')
        );

        $this->em->persist(
            (new Teacher())
                ->setExternalId('AB')
                ->setAcronym('AB')
                ->setFirstname('Firstname')
                ->setLastname('Lastname')
                ->setGender(Gender::Female())
        );
        $this->em->persist(
            (new Teacher())
                ->setExternalId('AC')
                ->setAcronym('AC')
                ->setFirstname('Firstname')
                ->setLastname('Lastname')
                ->setGender(Gender::Male())
        );
        $this->em->flush();
    }

    public function testImport() {
        $teachersData = [
            (new TeacherData())
                ->setId('AB')
                ->setAcronym('AB')
                ->setFirstname('John')
                ->setLastname('Doe')
                ->setGender('male')
                ->setTags(['tag1']),
            (new TeacherData())
                ->setId('AD')
                ->setAcronym('AD')
                ->setFirstname('John')
                ->setLastname('Doe')
                ->setGender('male')
                ->setTags(['tag1', 'tag2']),
        ];

        $repository = new TeacherRepository($this->em);
        $tagRepository = new TeacherTagRepository($this->em);
        $importer = new Importer();
        $strategy = new TeachersImportStrategy($repository, $tagRepository);
        $result = $importer->import($teachersData, $strategy);

        /** @var Teacher[] $addedTeachers */
        $addedTeachers = $result->getAdded();
        $this->assertEquals(1, count($addedTeachers));
        $this->assertEquals('AD', $addedTeachers[0]->getAcronym());
        $this->assertEquals(2, $addedTeachers[0]->getTags()->count());

        /** @var Teacher[] $updatedTeachers */
        $updatedTeachers = $result->getUpdated();
        $this->assertEquals(1, count($updatedTeachers));
        $this->assertEquals('AB', $updatedTeachers[0]->getAcronym());
        $this->assertEquals(1, $updatedTeachers[0]->getTags()->count());

        /** @var Teacher[] $removedTeachers */
        $removedTeachers = $result->getRemoved();
        $this->assertEquals(1, count($removedTeachers));
        $this->assertEquals('AC', $removedTeachers[0]->getAcronym());
    }
}