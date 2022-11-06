<?php

namespace App\Tests\Import;

use App\Entity\Gender;
use App\Entity\Section;
use App\Entity\Teacher;
use App\Entity\TeacherTag;
use App\Import\Importer;
use App\Import\TeachersImportStrategy;
use App\Repository\ImportDateTypeRepository;
use App\Repository\SectionRepository;
use App\Repository\SubjectRepository;
use App\Repository\TeacherRepository;
use App\Repository\TeacherTagRepository;
use App\Request\Data\TeacherData;
use App\Request\Data\TeachersData;
use DateTime;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TeachersImportStrategyTest extends WebTestCase {

    private $em;
    private $validator;

    public function setUp(): void {
        $kernel = static::createKernel();
        $kernel->boot();

        $this->validator = $kernel
            ->getContainer()
            ->get('test.service_container')
            ->get('validator');

        $this->em = $kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager();

        $section = (new Section())
            ->setYear(2020)
            ->setNumber(1)
            ->setDisplayName('Testabschnitt')
            ->setStart(new DateTime('2020-08-31'))
            ->setEnd(new DateTime('2021-01-31'));
        $this->em->persist($section);

        $tag1 = (new TeacherTag())
            ->setExternalId('tag1')
            ->setName('Tag 1')
            ->setColor('#000000');

        $this->em->persist($tag1);

        $this->em->persist(
            (new TeacherTag())
                ->setExternalId('tag2')
                ->setName('Tag 2')
                ->setColor('#111111')
        );

        $teacher1 = (new Teacher())
            ->setExternalId('AB')
            ->setAcronym('AB')
            ->setFirstname('Firstname')
            ->setLastname('Lastname')
            ->setGender(Gender::Female());
        $teacher1->addSection($section);

        $this->em->persist($teacher1);
        $teacher = (new Teacher())
            ->setExternalId('AC')
            ->setAcronym('AC')
            ->setFirstname('Firstname')
            ->setLastname('Lastname')
            ->setGender(Gender::Male());
        $teacher->addTag($tag1);
        $teacher->addSection($section);
        $this->em->persist($teacher);
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
        $subjectRepository = new SubjectRepository($this->em);
        $tagRepository = new TeacherTagRepository($this->em);
        $dateTimeRepository = new ImportDateTypeRepository($this->em);
        $sectionRepository = new SectionRepository($this->em);
        $importer = new Importer($this->validator, $dateTimeRepository, new NullLogger());
        $strategy = new TeachersImportStrategy($repository, $subjectRepository, $tagRepository, $sectionRepository);
        $result = $importer->import((new TeachersData())->setTeachers($teachersData)->setYear(2020)->setSection(1), $strategy);

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

    public function testImportDoesNotRemoveTags() {
        $teachersData = [
            (new TeacherData())
                ->setId('AC')
                ->setAcronym('AC')
                ->setFirstname('John')
                ->setLastname('Doe')
                ->setGender('male')
                ->setTags(['tag2']),
        ];

        $repository = new TeacherRepository($this->em);
        $subjectRepository = new SubjectRepository($this->em);
        $tagRepository = new TeacherTagRepository($this->em);
        $dateTimeRepository = new ImportDateTypeRepository($this->em);
        $sectionRepository = new SectionRepository($this->em);
        $importer = new Importer($this->validator, $dateTimeRepository, new NullLogger());
        $strategy = new TeachersImportStrategy($repository, $subjectRepository, $tagRepository, $sectionRepository);
        $result = $importer->import((new TeachersData())->setTeachers($teachersData)->setYear(2020)->setSection(1), $strategy);

        /** @var Teacher[] $updatedTeachers */
        $updatedTeachers = $result->getUpdated();
        $this->assertEquals(1, count($updatedTeachers));
        $this->assertEquals('AC', $updatedTeachers[0]->getAcronym());
        $this->assertEquals(2, $updatedTeachers[0]->getTags()->count());
    }
}