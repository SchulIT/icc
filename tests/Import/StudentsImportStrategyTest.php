<?php

namespace App\Tests\Import;

use App\Entity\Grade;
use App\Import\Importer;
use App\Import\StudentsImportStrategy;
use App\Repository\GradeRepository;
use App\Repository\PrivacyCategoryRepository;
use App\Repository\StudentRepository;
use App\Request\Data\StudentData;
use App\Request\Data\StudentsData;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StudentsImportStrategyTest extends WebTestCase {

    /**
     * @expectedException \App\Import\ImportException
     * @expectedExceptionMessage Grade "Q2" does not exist (Student ID: "2", Lastname: "Housecoat")
     */
    public function testImportMissingGrade() {
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
                ->setIsFullAged(false)
                ->setStatus(1)
                ->setGender('male'),
            (new StudentData())
                ->setId('2')
                ->setFirstname('Sally')
                ->setLastname('Housecoat')
                ->setGrade('Q2')
                ->setIsFullAged(true)
                ->setStatus(1)
                ->setGender('female'),
        ];

        $strategy = new StudentsImportStrategy($repository, $gradeRepository, $privacyRepository);
        $importer = new Importer($kernel->getContainer()->get('validator'));
        $importer->import((new StudentsData())->setStudents($data), $strategy);
    }
}