<?php

namespace App\Tests\Untis\Gpu\Supervision;

use App\Import\Importer;
use App\Import\ImportResult;
use App\Import\TimetableSupervisionsImportStrategy;
use App\Request\Data\TimetableSupervisionData;
use App\Request\Data\TimetableSupervisionsData;
use App\Settings\UntisSettings;
use App\Untis\Gpu\Supervision\Supervision;
use App\Untis\Gpu\Supervision\SupervisionImporter;
use App\Untis\Gpu\Supervision\SupervisionReader;
use DateTime;
use League\Csv\Reader;
use PHPUnit\Framework\TestCase;
use stdClass;

class SupervisionImporterTest extends TestCase {

    private function getUntisSettings(): UntisSettings {
        $settings = $this->createStub(UntisSettings::class);
        $settings->method('getWeekMap')
            ->willReturn([
                [
                    'school_week' => 1,
                    'calendar_week' => 32
                ],
                [
                    'school_week' => 2,
                    'calendar_week' => 33
                ],
                [
                    'school_week' => 3,
                    'calendar_week' => 34
                ],
                [
                    'school_week' => 4,
                    'calendar_week' => 35
                ]
            ]);

        return $settings;
    }

    public function testImport() {
        $strategy = $this->createMock(TimetableSupervisionsImportStrategy::class);

        $start = new DateTime('2022-08-10');
        $end = new DateTime('2022-09-02');

        $importer = $this->createMock(Importer::class);
        $importer
            ->expects($this->once())
            ->method('replaceImport')
            ->with(
                $this->callback(function(TimetableSupervisionsData $data) use ($start, $end) {
                    $this->assertCount(6, $data->getSupervisions(), 'Ensure 6 supervisions are created');

                    $abcSupervisions = array_filter($data->getSupervisions(), function(TimetableSupervisionData $supervisionData) {
                        return $supervisionData->getTeacher() === 'ABC';
                    });

                    $this->assertCount(2, $abcSupervisions);

                    foreach($abcSupervisions as $supervision) {
                        $this->assertEquals(1, (int)$supervision->getDate()->format('w'), 'Ensure ABC has supervisions on mondays');
                        $this->assertTrue($start <= $supervision->getDate(), 'Ensure ABCs supervisions are after start date');
                        $this->assertTrue($supervision->getDate() <= $end, 'Ensure ABCs supervisions are before end date');
                        $this->assertEquals(1, $supervision->getLesson());
                        $this->assertEquals('Flur', $supervision->getLocation());
                    }

                    $bcdSupervisions = array_filter($data->getSupervisions(), function(TimetableSupervisionData $supervisionData) {
                        return $supervisionData->getTeacher() === 'BCD';
                    });

                    $this->assertCount(4, $bcdSupervisions);

                    foreach($bcdSupervisions as $supervision) {
                        $this->assertEquals(3, (int)$supervision->getDate()->format('w'), 'Ensure BCD has supervisions on wednesdays');
                        $this->assertTrue($start <= $supervision->getDate(), 'Ensure BCDs supervisions are after start date');
                        $this->assertTrue($supervision->getDate() <= $end, 'Ensure BCDs supervisions are before end date');
                        $this->assertEquals(7, $supervision->getLesson());
                        $this->assertEquals('Hof', $supervision->getLocation());
                    }

                    return true;
                }),
                $this->identicalTo($strategy)
            );

        $reader = $this->createMock(SupervisionReader::class);
        $reader->method('readGpu')
            ->withAnyParameters()
            ->willReturn([
                (new Supervision())
                ->setCorridor('Flur')
                ->setDay(1)
                ->setLesson(1)
                ->setTeacher('ABC')
                ->setWeeks([1,2,3]),
                (new Supervision())
                ->setCorridor('Hof')
                ->setDay(3)
                ->setLesson(7)
                ->setTeacher('BCD')
                ->setWeeks([])
            ]);

        $supervisionsImporter = new SupervisionImporter($importer, $strategy, $reader, $this->getUntisSettings());
        $supervisionsImporter->import(Reader::createFromString(''), $start, $end);


    }
}