<?php

namespace App\Tests\Import;

use App\Entity\Exam;
use App\Entity\ExamSupervision;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupType;
use App\Entity\Teacher;
use App\Entity\Tuition;
use App\Import\ExamsImportStrategy;
use App\Import\Importer;
use App\Repository\ExamRepository;
use App\Repository\ImportDateTypeRepository;
use App\Repository\RoomRepositoryInterface;
use App\Repository\StudentRepository;
use App\Repository\TeacherRepository;
use App\Repository\TuitionRepository;
use App\Request\Data\ExamData;
use App\Request\Data\ExamsData;
use DateTime;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ExamsImportStrategyTest extends WebTestCase {

    private $validator;
    private $em;

    public function setUp(): void {
        $kernel = static::bootKernel();

        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->validator = $kernel
            ->getContainer()
            ->get('validator');

        $teacher1 = (new Teacher())
            ->setExternalId('TEST1')
            ->setAcronym('TEST1')
            ->setFirstname('Test')
            ->setLastname('Lehrer');
        $this->em->persist($teacher1);

        $this->em->persist(
            (new Teacher())
                ->setExternalId('TEST2')
                ->setAcronym('TEST2')
                ->setFirstname('Test')
                ->setLastname('Lehrerin')
        );

        $this->em->persist(
            (new Student())
                ->setExternalId("1")
                ->setFirstname('Test')
                ->setLastname('Schülerin')
        );

        $studyGroup = (new StudyGroup())
            ->setExternalId('TEST')
            ->setName('Testgruppe')
            ->setType(StudyGroupType::Course());
        $this->em->persist($studyGroup);

        $tuition = (new Tuition())
            ->setExternalId('TEST')
            ->setName('Testkurs')
            ->setDisplayName('Testkurs')
            ->setStudyGroup($studyGroup)
            ->setTeacher($teacher1);

        $this->em->persist($tuition);
        $this->em->flush();
    }

    private function getDefaultImportData(): ExamsData {
        return (new ExamsData())
            ->setExams([
                (new ExamData())
                    ->setId('TEST')
                    ->setDate(new DateTime('2020-12-30'))
                    ->setLessonStart(3)
                    ->setLessonEnd(4)
                    ->setTuitions(['TEST'])
                    ->setStudents(['1', '2'])
                    ->setSupervisions(['TEST1', 'TEST2'])
                    ->setRooms([])
            ]);
    }

    private function getStrategy(): ExamsImportStrategy {
        return new ExamsImportStrategy(
            new ExamRepository($this->em),
            new TuitionRepository($this->em),
            new StudentRepository($this->em),
            new TeacherRepository($this->em),
            $this->getMockBuilder(EventDispatcherInterface::class)->getMock(),
            $this->getMockBuilder(RoomRepositoryInterface::class)->getMock()
        );
    }

    public function testSimpleImport() {
        $strategy = $this->getStrategy();
        $importer = new Importer($this->validator, new ImportDateTypeRepository($this->em), new NullLogger());
        $result = $importer->import($this->getDefaultImportData(), $strategy);

        $this->assertEquals(1, count($result->getAdded()));

        /** @var Exam $exam */
        $exam = $result->getAdded()[0];

        $this->assertEquals('TEST', $exam->getExternalId());
        $this->assertEquals(new DateTime('2020-12-30'), $exam->getDate());
        $this->assertEquals(3, $exam->getLessonStart());
        $this->assertEquals(4, $exam->getLessonEnd());
        $this->assertNull($exam->getRoom());
        $this->assertNotNull($exam->getTuitions()->first());
        $this->assertEquals('TEST', $exam->getTuitions()->first()->getExternalId());
        $this->assertEquals(1, count($exam->getStudents()));

        /** @var ExamSupervision $firstSupervision */
        $firstSupervision = $exam->getSupervisions()->get(0);

        /** @var ExamSupervision $secondSupervision */
        $secondSupervision = $exam->getSupervisions()->get(1);

        $this->assertNotNull($firstSupervision);
        $this->assertEquals(3, $firstSupervision->getLesson());
        $this->assertEquals('TEST1', $firstSupervision->getTeacher()->getAcronym());

        $this->assertNotNull($secondSupervision);
        $this->assertEquals(4, $secondSupervision->getLesson());
        $this->assertEquals('TEST2', $secondSupervision->getTeacher()->getAcronym());
    }

    public function testImportAndUpdate() {
        $strategy = $this->getStrategy();
        $importer = new Importer($this->validator, new ImportDateTypeRepository($this->em), new NullLogger());
        $importer->import($this->getDefaultImportData(), $strategy);

        $updateData = (new ExamsData())
            ->setExams([
                (new ExamData())
                    ->setId('TEST')
                    ->setDate(new DateTime('2020-12-30'))
                    ->setLessonStart(1)
                    ->setLessonEnd(3)
                    ->setTuitions(['TEST'])
                    ->setStudents(['1', '2'])
                    ->setSupervisions(['TEST2', 'TEST2', 'TEST1'])
                    ->setRooms([])
            ]);

        $result = $importer->import($updateData, $strategy);
        $this->assertEquals(1, count($result->getUpdated()));

        /** @var Exam $exam */
        $exam = $result->getUpdated()[0];

        $this->assertEquals('TEST', $exam->getExternalId());
        $this->assertEquals(new DateTime('2020-12-30'), $exam->getDate());
        $this->assertEquals(1, $exam->getLessonStart());
        $this->assertEquals(3, $exam->getLessonEnd());
        $this->assertEquals(3, count($exam->getSupervisions()));

        // Sort supervisions to ensure lessons are correct in later assertions
        /** @var ExamSupervision[] $supervisions */
        $supervisions = $exam->getSupervisions()->toArray();
        usort($supervisions, function(ExamSupervision $supervisionA, ExamSupervision $supervisionB) {
            return $supervisionA->getLesson() - $supervisionB->getLesson();
        });

        $firstSupervision = $supervisions[0];
        $secondSupervision = $supervisions[1];
        $thirdSupervision = $supervisions[2];

        $this->assertNotNull($firstSupervision);
        $this->assertEquals(1, $firstSupervision->getLesson());
        $this->assertEquals('TEST2', $firstSupervision->getTeacher()->getAcronym());

        $this->assertNotNull($secondSupervision);
        $this->assertEquals(2, $secondSupervision->getLesson());
        $this->assertEquals('TEST2', $secondSupervision->getTeacher()->getAcronym());

        $this->assertNotNull($thirdSupervision);
        $this->assertEquals(3, $thirdSupervision->getLesson());
        $this->assertEquals('TEST1', $thirdSupervision->getTeacher()->getAcronym());
    }
}