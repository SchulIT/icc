<?php

namespace App\Tests\Import;

use App\Entity\Grade;
use App\Import\Importer;
use App\Import\StudentsImportStrategy;
use App\Repository\GradeRepository;
use App\Repository\ImportDateTypeRepository;
use App\Repository\PrivacyCategoryRepository;
use App\Repository\StudentRepository;
use App\Request\Data\StudentData;
use App\Request\Data\StudentsData;
use DateTime;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StudentsImportStrategyTest extends WebTestCase {

    public function testImportMissingGrade() {
        $this->expectExceptionMessage("Grade \"Q2\" does not exist (Student ID: \"2\", Lastname: \"Housecoat\")");
        $this->expectException(\App\Import\ImportException::class);

        $kernel = static::createKernel();
        $kernel->boot();

        $em = $kernel->getContainer()->get('doctrine')
            ->getManager();
        $repository = new StudentRepository($em);
        $gradeRepository = new GradeRepository($em);
        $privacyRepository = new PrivacyCategoryRepository($em);

        $em->persist(
            (new Grade())
                ->setExternalId('EF')
                ->setName('EF')
        );
        $em->flush();

        $data = [
            (new StudentData())
                ->setId('1')
                ->setFirstname('John')
                ->setLastname('Doe')
                ->setGrade('EF')
                ->setBirthday((new DateTime())->modify('-10 year'))
                ->setStatus(1)
                ->setGender('male'),
            (new StudentData())
                ->setId('2')
                ->setFirstname('Sally')
                ->setLastname('Housecoat')
                ->setGrade('Q2')
                ->setBirthday((new DateTime())->modify('-20 year'))
                ->setStatus(1)
                ->setGender('female'),
        ];

        $dateTimeRepository = new ImportDateTypeRepository($kernel->getContainer()->get('doctrine')->getManager());
        $strategy = new StudentsImportStrategy($repository, $gradeRepository, $privacyRepository);
        $importer = new Importer($kernel->getContainer()->get('validator'), $dateTimeRepository, new NullLogger());
        $importer->import((new StudentsData())->setStudents($data), $strategy);
    }
}