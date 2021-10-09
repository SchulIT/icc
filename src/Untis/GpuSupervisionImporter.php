<?php

namespace App\Untis;

use App\Entity\TimetablePeriod;
use App\Import\Importer;
use App\Import\ImportResult;
use App\Import\TimetableSupervisionsImportStrategy;
use App\Request\Data\TimetableSupervisionData;
use App\Request\Data\TimetableSupervisionsData;
use App\Settings\UntisSettings;
use League\Csv\Reader;
use Ramsey\Uuid\Uuid;

class GpuSupervisionImporter {
    private $importer;
    private $strategy;
    private $gpuReader;
    private $settings;

    public function __construct(Importer $importer, TimetableSupervisionsImportStrategy $strategy, SupervisionReader $gpuReader, UntisSettings $settings) {
        $this->importer = $importer;
        $this->strategy = $strategy;
        $this->gpuReader = $gpuReader;
        $this->settings = $settings;
    }

    public function import(Reader $reader, TimetablePeriod $period): ImportResult {
        $data = new TimetableSupervisionsData();
        $data->setPeriod($period->getExternalId());
        $supervisions = [ ];

        $periodWeeks = $this->getPeriodCalendarWeeks($period);
        $map = $this->getWeekMap();

        foreach($this->gpuReader->readGpu($reader) as $supervision) {
            $supervisionData = (new TimetableSupervisionData())
                ->setId(Uuid::uuid4()->toString())
                ->setLocation($supervision->getCorridor())
                ->setTeacher($supervision->getTeacher())
                ->setDay($supervision->getDay())
                ->setLesson($supervision->getLesson())
                ->setIsBefore(true);

            if(count($supervision->getWeeks()) === 0) {
                $supervisionData->setWeeks($periodWeeks);
            } else {
                $supervisionData->setWeeks(array_map(function($week) use ($map) {
                    return $map[$week];
                }, $supervision->getWeeks()));
            }

            $supervisions[] = $supervisionData;
        }

        $data->setSupervisions($supervisions);

        return $this->importer->import($data, $this->strategy);
    }

    private function getWeekMap(): array {
        $map = [ ];

        foreach($this->settings->getWeekMap() as $week) {
            $map[$week['school_week']] = $week['calendar_week'];
        }

        return $map;
    }

    private function getPeriodCalendarWeeks(TimetablePeriod $period): array {
        $weeks = [ ];

        $current = clone $period->getStart();
        while($current < $period->getEnd()) {
            $weeks[] = intval($current->format('W'));
            $current = $current->modify('+7 days');
        }

        return $weeks;
    }
}