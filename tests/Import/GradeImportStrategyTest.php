<?php

namespace App\Tests\Import;

use App\Entity\Grade;
use App\Import\GradesImportStrategy;
use App\Import\Importer;
use App\Repository\GradeRepository;
use App\Repository\ImportDateTypeRepository;
use App\Repository\SectionRepository;
use App\Request\Data\GradeData;
use App\Request\Data\GradesData;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GradeImportStrategyTest extends WebTestCase {
    public function testImport() {
        $kernel = static::createKernel();
        $kernel->boot();

        $em = $kernel->getContainer()->get('doctrine')
            ->getManager();
        $repository = new GradeRepository($em);
        $sectionRepository = new SectionRepository($em);

        $em->persist(
            (new Grade())
                ->setExternalId('05A')
                ->setName('5A')
        );
        $em->persist(
            (new Grade())
                ->setExternalId('EF')
                ->setName('EF')
        );
        $em->flush();

        $gradeData = [
            (new GradeData())
                ->setId('05A')
                ->setName('05A'),
            (new GradeData())
                ->setId('Q1')
                ->setName('Q1')
        ];

        $strategy = new GradesImportStrategy($repository, $sectionRepository);
        $dateTimeRepository = new ImportDateTypeRepository($kernel->getContainer()->get('doctrine')->getManager());
        $importer = new Importer($kernel->getContainer()->get('test.service_container')->get('validator'), $dateTimeRepository, new NullLogger());
        $result = $importer->import((new GradesData())->setGrades($gradeData), $strategy);

        $this->assertNotNull($result);
        $this->assertEquals(1, count($result->getAdded()));
        $this->assertEquals(1, count($result->getUpdated()));
        $this->assertEquals(1, count($result->getRemoved()));

        /** @var Grade $added */
        $added = $result->getAdded()[0];
        $this->assertNotNull($added);
        $this->assertEquals('Q1', $added->getExternalId());
        $this->assertEquals('Q1', $added->getName());

        /** @var Grade $updated */
        $updated = $result->getUpdated()[0];
        $this->assertNotNull($updated);
        $this->assertEquals('05A', $updated->getExternalId());
        $this->assertEquals('05A', $updated->getName());

        /** @var Grade $removed */
        $removed = $result->getRemoved()[0];
        $this->assertNotNull($removed);
        $this->assertEquals('EF', $removed->getExternalId());
    }
}