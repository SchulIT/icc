<?php

namespace App\Untis\Gpu\Supervision;

use App\Import\Importer;
use App\Import\ImportResult;
use App\Import\TimetableSupervisionsImportStrategy;
use App\Request\Data\TimetableSupervisionData;
use App\Request\Data\TimetableSupervisionsData;
use App\Settings\UntisSettings;
use DateTime;
use League\Csv\Reader;
use Ramsey\Uuid\Uuid;

class SupervisionImporter {
    private Importer $importer;
    private TimetableSupervisionsImportStrategy $strategy;
    private SupervisionReader $gpuReader;
    private UntisSettings $settings;

    public function __construct(Importer $importer, TimetableSupervisionsImportStrategy $strategy,
                                SupervisionReader $gpuReader, UntisSettings $settings) {
        $this->importer = $importer;
        $this->strategy = $strategy;
        $this->gpuReader = $gpuReader;
        $this->settings = $settings;
    }

    public function import(Reader $reader, DateTime $start, DateTime $end): ImportResult {
        $data = new TimetableSupervisionsData();
        $data->setStartDate($start);
        $data->setEndDate($end);

        $supervisions = [ ];

        $map = $this->getWeekMap();

        foreach($this->gpuReader->readGpu($reader) as $supervision) {
            $dates = [ ];
            if(count($supervision->getWeeks()) === 0) {
                $dates = $this->getDatesForRange($supervision->getDay(), $start, $end);
            } else {
                $dates = $this->getDatesForSchoolWeeks($supervision->getDay(), $supervision->getWeeks(), $map, $start, $end);
            }

            foreach($dates as $date) {
                $supervisionData = (new TimetableSupervisionData())
                    ->setId(Uuid::uuid4()->toString())
                    ->setLocation($supervision->getCorridor())
                    ->setTeacher($supervision->getTeacher())
                    ->setDate($date)
                    ->setLesson($supervision->getLesson())
                    ->setIsBefore(true);

                $supervisions[] = $supervisionData;
            }
        }

        $data->setSupervisions($supervisions);

        return $this->importer->replaceImport($data, $this->strategy);
    }

    /**
     * @param int $day
     * @param DateTime $start
     * @param DateTime $end
     * @return DateTime[]
     */
    private function getDatesForRange(int $day, DateTime $start, DateTime $end): array {
        $dates = [ ];
        $current = clone $start;

        while((int)$current->format('w') !== $day) {
            $current = $current->modify('+1 day');
        }

        while($current <= $end) {
            $dates[] = clone $current;
            $current = $current->modify('+7 days');
        }

        return $dates;
    }

    /**
     * @param int $day
     * @param int[] $weeks
     * @param array $map
     * @return DateTime[]
     */
    private function getDatesForSchoolWeeks(int $day, array $weeks, array $map, DateTime $start, DateTime $end): array {
        $dates = [ ];
        $calendarWeeks = array_map(function($week) use($map) {
            return $map[$week];
        }, $weeks);

        $current = clone $start;
        while($current->format('w') != $day) {
            $current = $current->modify('+1 day');
        }

        while($current <= $end) {
            if(in_array($current->format('W'), $calendarWeeks)) {
                $dates[] = clone $current;
            }

            $current = $current->modify('+7 days');
        }

        return $dates;
    }

    private function getWeekMap(): array {
        $map = [ ];

        foreach($this->settings->getWeekMap() as $week) {
            $map[$week['school_week']] = $week['calendar_week'];
        }

        return $map;
    }
}