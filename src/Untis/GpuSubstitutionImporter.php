<?php

namespace App\Untis;

use App\Import\Importer;
use App\Import\ImportResult;
use App\Import\SubstitutionsImportStrategy;
use App\Request\Data\SubstitutionData;
use App\Request\Data\SubstitutionsData;
use App\Settings\UntisSettings;
use App\Utils\ArrayUtils;
use DateTime;
use League\Csv\Reader;

class GpuSubstitutionImporter {
    private $importer;
    private $strategy;
    private $gpuReader;
    private $settings;

    public function __construct(Importer $importer, SubstitutionsImportStrategy $strategy, SubstitutionReader $gpuReader, UntisSettings $settings) {
        $this->importer = $importer;
        $this->strategy = $strategy;
        $this->gpuReader = $gpuReader;
        $this->settings = $settings;
    }

    public function import(Reader $reader, DateTime $start, DateTime $end, bool $suppressNotifications): ImportResult {
        $start->setTime(0,0,0);
        $end->setTime(0,0,0);

        $data = new SubstitutionsData();
        $data->setSuppressNotifications($suppressNotifications);
        $substitutions = [ ];

        $subjectOverrideMap = $this->getSubjectOverridesMap();

        foreach($this->gpuReader->readGpu($reader) as $substitution) {
            if($substitution->getDate() < $start || $substitution->getDate() > $end) {
                continue;
            }

            if($substitution->getId() === 0) {
                continue;
            }

            $substitutionData = (new SubstitutionData())
                ->setId((string)$substitution->getId())
                ->setDate($substitution->getDate())
                ->setLessonStart($substitution->getLesson())
                ->setLessonEnd($substitution->getLesson())
                ->setRooms($substitution->getRooms())
                ->setReplacementRooms($substitution->getReplacementRooms())
                ->setGrades($substitution->getGrades())
                ->setReplacementGrades($substitution->getReplacementGrades())
                ->setText($substitution->getRemark())
                ->setType($this->getType($substitution));

            if($substitution->getTeacher() !== null) {
                $substitutionData->setTeachers([$substitution->getTeacher()]);
            } else {
                $substitutionData->setTeachers([]);
            }

            if($substitution->getReplacementTeacher() !== null) {
                $substitutionData->setReplacementTeachers([$substitution->getReplacementTeacher()]);
            } else {
                $substitutionData->setReplacementTeachers([]);
            }

            if(!empty($substitution->getSubject()) && array_key_exists($substitution->getSubject(), $subjectOverrideMap)) {
                $substitutionData->setSubject($subjectOverrideMap[$substitution->getSubject()]);
            } else {
                $substitutionData->setSubject($substitution->getSubject());
            }

            if(!empty($substitution->getReplacementSubject()) && array_key_exists($substitution->getReplacementSubject(), $subjectOverrideMap)) {
                $substitutionData->setReplacementSubject($subjectOverrideMap[$substitution->getReplacementSubject()]);
            } else {
                $substitutionData->setReplacementSubject($substitution->getReplacementSubject());
            }

            if(!empty($substitution->getSubject()) && empty($substitution->getReplacementSubject())) {
                $substitutionData->setReplacementSubject($substitutionData->getSubject());
            }

            $substitutionData->setStartsBefore($substitutionData->getType() === 'Pausenaufsichtsvertretung');

            if($this->matchesFlag($substitution->getFlags(), GpuSubstitutionFlag::DoNotPrintFlag)) {
                continue;
            }

            $substitutions[] = $substitutionData;
        }

        $data->setSubstitutions($substitutions);

        $result = $this->importer->import($data, $this->strategy);
        return $result;
    }

    private function getSubjectOverridesMap(): array {
        $map = [ ];

        foreach($this->settings->getSubjectOverrides() as $override) {
            $map[$override['untis']] = $override['override'];
        }

        return $map;
    }

    private function getType(GpuSubstitution $substitution): string {
        $map = [
            'S' => 'Betreuung',
            'A' => 'Sondereinsatz',
            'L' => 'Freisetzung',
            'R' => 'Raumvertretung',
            'B' => 'Pausenaufsichtsvertretung',
            'E' => 'Klausur'
        ];

        if($substitution->getType() !== null && array_key_exists($substitution->getType()->getValue(), $map)) {
            return $map[$substitution->getType()->getValue()];
        }

        $map = [
            GpuSubstitutionFlag::Cancellation => 'Entfall',
            GpuSubstitutionFlag::Supervision => 'Betreuung',
            GpuSubstitutionFlag::SpecialDuty => 'Sondereinsatz',
            GpuSubstitutionFlag::ShiftedFrom => 'Vertretung',
            GpuSubstitutionFlag::Release => 'Freisetzung',
            GpuSubstitutionFlag::PlusAsStandIn => 'Plus als Vertreter',
            GpuSubstitutionFlag::PartialStandIn => 'Teilvertretung',
            GpuSubstitutionFlag::ShiftedTo => 'Vertretung',
            GpuSubstitutionFlag::RoomExchange => 'Raumvertretung',
            GpuSubstitutionFlag::SupervisionExchange => 'Pausenaufsichtsvertretung',
            GpuSubstitutionFlag::NoLesson => 'Unterrichtsfrei'
        ];

        foreach($map as $flag => $value) {
            if($this->matchesFlag($substitution->getFlags(), $flag)) {
                return $value;
            }
        }

        return 'Vertretung';
    }

    private function matchesFlag(int $value, int $flag): bool {
        return ($value & $flag) == $flag;
    }
}