<?php

namespace App\Tests\Untis;

use App\Import\ExamsImportStrategy;
use App\Import\Importer;
use App\Import\ImportResult;
use App\Request\Data\ExamsData;
use App\Settings\UntisSettings;
use App\Untis\Gpu\Exam\Exam;
use App\Untis\Gpu\Exam\ExamImporter;
use App\Untis\Gpu\Exam\ExamReader;
use App\Untis\Gpu\Tuition\Tuition;
use App\Untis\Gpu\Tuition\TuitionReader;
use DateTime;
use Hoa\File\Read;
use League\Csv\Reader;
use PHPUnit\Framework\TestCase;
use stdClass;

class GpuExamImporterTest extends TestCase {

    private function getExamReaderMock(): ExamReader {
        $examReader = $this->getMockBuilder(ExamReader::class)->getMock();
        $examReader->method('readGpu')
            ->willReturn([
                (new Exam())
                    ->setId(1)
                    ->setName('Kla-NT-Foo')
                    ->setTuitions(['1'])
                    ->setDate(new DateTime('2021-01-01'))
                    ->setLessonStart(1)
                    ->setLessonEnd(2)
                    ->setSubjects(['IF-GK1'])
                    ->setStudents([
                        'Mustermann_Max_20000101'
                    ]),
                (new Exam())
                    ->setId(2)
                    ->setTuitions(['1'])
                    ->setDate(new DateTime('2021-04-01'))
                    ->setLessonStart(1)
                    ->setLessonEnd(2)
                    ->setSubjects(['IF-GK1'])
                    ->setStudents([
                        'Mustermann_Max_20000101'
                    ])
            ]);

        return $examReader;
    }

    private function getTuitionReaderMock(): TuitionReader {
        $tuitionReader = $this->getMockBuilder(TuitionReader::class)->getMock();
        $tuitionReader->method('readGpu')
            ->willReturn([
                (new Tuition())->setId(1)->setGrade('EF')->setTeacher('TEST')->setSubject('IF-GK1')
            ]);

        return $tuitionReader;
    }

    public function testImportStudentsFromTuitionAlwaysImportTrueAndIgnoreRegExpNotMatching() {
        $importer = $this->getMockBuilder(Importer::class)->disableOriginalConstructor()->getMock();
        $strategy = $this->getMockBuilder(ExamsImportStrategy::class)->disableOriginalConstructor()->getMock();

        $settings = $this->getMockBuilder(UntisSettings::class)->disableOriginalConstructor()->getMock();
        $settings->method('alwaysImportExamWriters')
            ->willReturn(true);
        $settings->method('getIgnoreStudentOptionRegExp')
            ->willReturn('');

        $importer->expects($this->once())
            ->method('import')
            ->will($this->returnCallback(function(ExamsData $data) {
                $this->assertTrue($data->isSuppressNotifications());
                $this->assertEquals(2, count($data->getExams()));

                $examData = $data->getExams()[0];
                $this->assertEquals(1, count($examData->getStudents()), 'Klausur 1 should contain students');
                $examData = $data->getExams()[1];
                $this->assertEquals(1, count($examData->getStudents()), 'Klausur 2 should contain students');

                return new ImportResult([], [], [], [], new stdClass());
            }));

        $gpuExamImporter = new ExamImporter($importer, $strategy, $this->getExamReaderMock(), $this->getTuitionReaderMock(), $settings);
        $gpuExamImporter->import(Reader::createFromString(''), Reader::createFromString(''), new DateTime('2021-01-01'), new DateTime('2021-08-01'), true);
    }

    public function testImportStudentsFromTuitionAlwaysImportTrueAndIgnoreRegExpMatching() {
        $importer = $this->getMockBuilder(Importer::class)->disableOriginalConstructor()->getMock();
        $strategy = $this->getMockBuilder(ExamsImportStrategy::class)->disableOriginalConstructor()->getMock();

        $settings = $this->getMockBuilder(UntisSettings::class)->disableOriginalConstructor()->getMock();
        $settings->method('alwaysImportExamWriters')
            ->willReturn(true);
        $settings->method('getIgnoreStudentOptionRegExp')
            ->willReturn('.*-NT-.*');

        $importer->expects($this->once())
            ->method('import')
            ->will($this->returnCallback(function(ExamsData $data) {
                $this->assertTrue($data->isSuppressNotifications());
                $this->assertEquals(2, count($data->getExams()));

                $examData = $data->getExams()[0];
                $this->assertEquals(0, count($examData->getStudents()), 'Klausur 1 should not contain any students');
                $examData = $data->getExams()[1];
                $this->assertEquals(1, count($examData->getStudents()), 'Klausur 2 should contain students');

                return new ImportResult([], [], [], [], new stdClass());
            }));

        $gpuExamImporter = new ExamImporter($importer, $strategy, $this->getExamReaderMock(), $this->getTuitionReaderMock(), $settings);
        $gpuExamImporter->import(Reader::createFromString(''), Reader::createFromString(''), new DateTime('2021-01-01'), new DateTime('2021-08-01'), true);
    }

    public function testImportStudentsFromTuitionNotAlwaysImportTrueAndIgnoreRegExpNotMatching() {
        $importer = $this->getMockBuilder(Importer::class)->disableOriginalConstructor()->getMock();
        $strategy = $this->getMockBuilder(ExamsImportStrategy::class)->disableOriginalConstructor()->getMock();

        $settings = $this->getMockBuilder(UntisSettings::class)->disableOriginalConstructor()->getMock();
        $settings->method('alwaysImportExamWriters')
            ->willReturn(false);
        $settings->method('getIgnoreStudentOptionRegExp')
            ->willReturn('');

        $importer->expects($this->once())
            ->method('import')
            ->will($this->returnCallback(function(ExamsData $data) {
                $this->assertTrue($data->isSuppressNotifications());
                $this->assertEquals(2, count($data->getExams()));

                $examData = $data->getExams()[0];
                $this->assertEquals(0, count($examData->getStudents()), 'Klausur 1 should not contain students');
                $examData = $data->getExams()[1];
                $this->assertEquals(0, count($examData->getStudents()), 'Klausur 2 should not contain students');

                return new ImportResult([], [], [], [], new stdClass());
            }));

        $gpuExamImporter = new ExamImporter($importer, $strategy, $this->getExamReaderMock(), $this->getTuitionReaderMock(), $settings);
        $gpuExamImporter->import(Reader::createFromString(''), Reader::createFromString(''), new DateTime('2021-01-01'), new DateTime('2021-08-01'), true);
    }

    public function testImportStudentsFromTuitionNotAlwaysImportTrueAndIgnoreRegExpMatching() {
        $importer = $this->getMockBuilder(Importer::class)->disableOriginalConstructor()->getMock();
        $strategy = $this->getMockBuilder(ExamsImportStrategy::class)->disableOriginalConstructor()->getMock();

        $settings = $this->getMockBuilder(UntisSettings::class)->disableOriginalConstructor()->getMock();
        $settings->method('alwaysImportExamWriters')
            ->willReturn(false);
        $settings->method('getIgnoreStudentOptionRegExp')
            ->willReturn('.*-NT-.*');

        $importer->expects($this->once())
            ->method('import')
            ->will($this->returnCallback(function(ExamsData $data) {
                $this->assertTrue($data->isSuppressNotifications());
                $this->assertEquals(2, count($data->getExams()));

                $examData = $data->getExams()[0];
                $this->assertEquals(1, count($examData->getStudents()), 'Klausur 1 should contain students');
                $examData = $data->getExams()[1];
                $this->assertEquals(0, count($examData->getStudents()), 'Klausur 2 should not contain any students');

                return new ImportResult([], [], [], [], new stdClass());
            }));

        $gpuExamImporter = new ExamImporter($importer, $strategy, $this->getExamReaderMock(), $this->getTuitionReaderMock(), $settings);
        $gpuExamImporter->import(Reader::createFromString(''), Reader::createFromString(''), new DateTime('2021-01-01'), new DateTime('2021-08-01'), true);
    }


}