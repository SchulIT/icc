<?php

namespace App\Tests\Import;

use App\Exam\Entity\Exam;
use App\Exam\Entity\ExamSupervision;
use App\Common\Entity\Grade;
use App\Common\Entity\Section;
use App\Common\Entity\Student;
use App\Common\Entity\StudyGroup;
use App\Common\Entity\StudyGroupType;
use App\Common\Entity\Subject;
use App\Common\Entity\Teacher;
use App\Common\Entity\Tuition;
use App\Exam\ExamStudentsResolver;
use App\Exam\Import\ExamsImportStrategy;
use App\Framework\Import\Importer;
use App\Exam\Repository\ExamRepository;
use App\Framework\Import\Repository\ImportDateTypeRepository;
use App\Book\Repository\RoomRepositoryInterface;
use App\Common\Repository\SectionRepository;
use App\Framework\Settings\Repository\SettingRepository;
use App\Common\Repository\StudentRepository;
use App\Common\Repository\TeacherRepository;
use App\Common\Repository\TuitionRepository;
use App\Request\Data\ExamData;
use App\Request\Data\ExamsData;
use App\Request\Data\ExamTuition;
use App\Common\Section\SectionResolver;
use App\Common\Settings\GeneralSettings;
use App\Framework\Import\Settings\ImportSettings;
use App\Framework\Settings\SettingsManager;
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
            ->get('test.service_container')
            ->get('validator');

        $section = (new Section())
            ->setYear(2020)
            ->setNumber(1)
            ->setDisplayName('Testabschnitt')
            ->setStart(new DateTime('2020-08-16'))
            ->setEnd(new DateTime('2021-01-31'));

        $this->em->persist($section);

        $teacher1 = (new Teacher())
            ->setExternalId('TEST1')
            ->setAcronym('TEST1')
            ->setFirstname('Test')
            ->setLastname('Lehrer');
        $teacher1->addSection($section);
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
                ->setBirthday((new DateTime())->modify('-10 year'))
        );

        $grade = (new Grade())
            ->setName('EF')
            ->setExternalId('EF');
        $this->em->persist($grade);

        $studyGroup = (new StudyGroup())
            ->setExternalId('TEST')
            ->setName('Testgruppe')
            ->setSection($section)
            ->setType(StudyGroupType::Course);
        $studyGroup->addGrade($grade);
        $this->em->persist($studyGroup);

        $subject = (new Subject())
            ->setExternalId('M')
            ->setName('M')
            ->setAbbreviation('M');
        $this->em->persist($subject);

        $tuition = (new Tuition())
            ->setExternalId('TEST')
            ->setName('Testkurs')
            ->setDisplayName('Testkurs')
            ->setSection($section)
            ->setStudyGroup($studyGroup)
            ->setSubject($subject);
        $tuition->addTeacher($teacher1);

        $this->em->persist($tuition);
        $this->em->flush();
    }

    private function getDefaultImportData(): ExamsData {
        return (new ExamsData())
            ->setStartDate(new DateTime('2020-12-01'))
            ->setEndDate(new DateTime('2020-12-31'))
            ->setExams([
                (new ExamData())
                    ->setId('TEST')
                    ->setDate(new DateTime('2020-12-30'))
                    ->setLessonStart(3)
                    ->setLessonEnd(4)
                    ->setTuitions([(new ExamTuition())->setTeachers(['TEST1'])->setGrades(['EF'])->setSubjectOrCourse('M')])
                    ->setStudents(['1', '2'])
                    ->setSupervisions(['TEST1', 'TEST2'])
                    ->setRooms([])
                    ->setComputeStudentsFromRules(false)
            ]);
    }

    private function getStrategy(): ExamsImportStrategy {
        $settingsManager = new SettingsManager(new SettingRepository($this->em));
        $sectionResolver = new SectionResolver(new GeneralSettings($settingsManager), new SectionRepository($this->em));
        $studentResolver = new ExamStudentsResolver(new ImportSettings($settingsManager), $sectionResolver);

        return new ExamsImportStrategy(
            new ExamRepository($this->em),
            new TuitionRepository($this->em),
            new StudentRepository($this->em),
            new TeacherRepository($this->em),
            $this->getMockBuilder(EventDispatcherInterface::class)->getMock(),
            $this->getMockBuilder(RoomRepositoryInterface::class)->getMock(),
            $sectionResolver,
            $studentResolver
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
            ->setStartDate(new DateTime('2020-12-01'))
            ->setEndDate(new DateTime('2020-12-31'))
            ->setExams([
                (new ExamData())
                    ->setId('TEST')
                    ->setDate(new DateTime('2020-12-30'))
                    ->setLessonStart(1)
                    ->setLessonEnd(3)
                    ->setTuitions([(new ExamTuition())->setTeachers(['TEST1'])->setGrades(['EF'])->setSubjectOrCourse('M')])
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