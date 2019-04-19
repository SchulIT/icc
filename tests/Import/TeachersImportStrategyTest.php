<?php

namespace App\Tests\Import;

use App\Entity\Gender;
use App\Entity\Teacher;
use App\Import\Importer;
use App\Import\TeachersImportStrategy;
use App\Repository\TeacherRepository;
use App\Request\Data\TeacherData;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TeachersImportStrategyTest extends WebTestCase {

    public function testImport() {
        $kernel = static::createKernel();
        $kernel->boot();

        $em = $kernel->getContainer()->get('doctrine')
            ->getManager();

        $em->persist(
            (new Teacher())
                ->setExternalId('AB')
                ->setAcronym('AB')
                ->setFirstname('Firstname')
                ->setLastname('Lastname')
                ->setGender(Gender::Female())
        );
        $em->persist(
            (new Teacher())
                ->setExternalId('AC')
                ->setAcronym('AC')
                ->setFirstname('Firstname')
                ->setLastname('Lastname')
                ->setGender(Gender::Male())
        );
        $em->flush();

        $teachersData = [
            (new TeacherData())
                ->setId('AB')
                ->setAcronym('AB')
                ->setFirstname('John')
                ->setLastname('Doe')
                ->setGender('male'),
            (new TeacherData())
                ->setId('AD')
                ->setAcronym('AD')
                ->setFirstname('John')
                ->setLastname('Doe')
                ->setGender('male'),
        ];

        $repository = new TeacherRepository($em);
        $importer = new Importer();
        $strategy = new TeachersImportStrategy($repository);
        $importer->import($teachersData, $strategy);

        $teachers = $strategy->getExistingEntities();

        $this->assertEquals(2, count($teachers));
        $this->assertArrayHasKey('AB', $teachers);
        $this->assertArrayHasKey('AD', $teachers);
    }
}