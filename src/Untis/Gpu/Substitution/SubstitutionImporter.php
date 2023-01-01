<?php

namespace App\Untis\Gpu\Substitution;

use App\Import\Importer;
use App\Import\ImportResult;
use App\Import\SubstitutionsImportStrategy;
use App\Request\Data\SubstitutionData;
use App\Request\Data\SubstitutionsData;
use App\Settings\UntisSettings;
use DateTime;
use League\Csv\Reader;

class SubstitutionImporter {
    public function __construct(private Importer $importer, private SubstitutionsImportStrategy $strategy, private SubstitutionReader $gpuReader, private UntisSettings $settings)
    {
    }

    private function sort(Substitution $substitutionA, Substitution $substitutionB): int {
        if($substitutionA->getDate() == $substitutionB->getDate()) {
            if(strnatcmp($substitutionA->getTeacher(), $substitutionB->getTeacher()) === 0) {
                if(strnatcmp($substitutionA->getSubject(),  $substitutionB->getSubject()) === 0) {
                    // Sort by lesson last
                    return $substitutionA->getLesson() - $substitutionB->getLesson();
                }

                return strnatcmp($substitutionA->getSubject(), $substitutionB->getSubject());
            }

            // Sort by teacher second
            return strnatcmp($substitutionA->getTeacher(), $substitutionB->getTeacher());
        }

        // Sort by date first
        return $substitutionA->getDate() < $substitutionB ? 1 : -1;
    }

    public function import(Reader $reader, DateTime $start, DateTime $end, bool $suppressNotifications): ImportResult {
        $start->setTime(0,0,0);
        $end->setTime(0,0,0);

        $data = new SubstitutionsData();
        $data->setSuppressNotifications($suppressNotifications);
        $substitutions = [ ];

        $subjectOverrideMap = $this->getSubjectOverridesMap();
        $gpuSubstitutions = $this->gpuReader->readGpu($reader);

        $gpuSubstitutions = array_filter($gpuSubstitutions, function(Substitution $substitution) use ($start, $end) {
            if($substitution->getDate() < $start || $substitution->getDate() > $end) {
                return false;
            }

            if($substitution->getId() === 0) {
                return false;
            }

            return true;
        });

        usort($gpuSubstitutions, [ $this, 'sort' ]);

        for($idx = 0; $idx < count($gpuSubstitutions); $idx++) {
            $substitution = $gpuSubstitutions[$idx];

            $includeNextLesson = false;

            if($this->settings->isSubstitutionCollapsingEnabled() && $idx+1 < count($gpuSubstitutions)) {
                $includeNextLesson = $this->isSubstitutionOfNextLesson($substitution, $gpuSubstitutions[$idx + 1]);
            }

            $substitutionData = (new SubstitutionData())
                ->setId((string)$substitution->getId())
                ->setDate($substitution->getDate())
                ->setLessonStart($substitution->getLesson())
                ->setLessonEnd($substitution->getLesson() + ($includeNextLesson ? 1 : 0))
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

            if($this->matchesFlag($substitution->getFlags(), SubstitutionFlag::DoNotPrintFlag)) {
                continue;
            }

            $substitutions[] = $substitutionData;

            if($includeNextLesson) {
                // Skip next substitution as it is already handled.
                $idx++;
            }
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

    private function getType(Substitution $substitution): string {
        $map = [
            'S' => 'Betreuung',
            'A' => 'Sondereinsatz',
            'L' => 'Freisetzung',
            'R' => 'Raumvertretung',
            'B' => 'Pausenaufsichtsvertretung',
            'E' => 'Klausur'
        ];

        if($substitution->getType() !== null && array_key_exists($substitution->getType()->value, $map)) {
            return $map[$substitution->getType()->value];
        }

        $map = [
            SubstitutionFlag::Cancellation => 'Entfall',
            SubstitutionFlag::Supervision => 'Betreuung',
            SubstitutionFlag::SpecialDuty => 'Sondereinsatz',
            SubstitutionFlag::ShiftedFrom => 'Vertretung',
            SubstitutionFlag::Release => 'Freisetzung',
            SubstitutionFlag::PlusAsStandIn => 'Plus als Vertreter',
            SubstitutionFlag::PartialStandIn => 'Teilvertretung',
            SubstitutionFlag::ShiftedTo => 'Vertretung',
            SubstitutionFlag::RoomExchange => 'Raumvertretung',
            SubstitutionFlag::SupervisionExchange => 'Pausenaufsichtsvertretung',
            SubstitutionFlag::NoLesson => 'Unterrichtsfrei'
        ];

        /**
         * @var int $flag
         * @var string $value
         */
        foreach($map as $flag => $value) {
            if($this->matchesFlag($substitution->getFlags(), $flag)) {
                if($flag === SubstitutionFlag::PlusAsStandIn && empty($substitution->getReplacementTeacher())) {
                    return 'Eigenverantwortliches Arbeiten';
                }

                return $value;
            }
        }

        return 'Vertretung';
    }

    private function matchesFlag(int $value, int $flag): bool {
        return ($value & $flag) == $flag;
    }

    /**
     * Checks whether the substitution are identical but their IDs and their lessons. These values of the second
     * substitution need to be increments of the ones of the first one.
     */
    private function isSubstitutionOfNextLesson(Substitution $first, Substitution $second): bool {
        return $first->getLesson() + 1 === $second->getLesson()
            && $first->getRooms() === $second->getRooms()
            && $first->getReplacementRooms() === $second->getReplacementRooms()
            && $first->getGrades() === $second->getGrades()
            && $first->getReplacementGrades() === $second->getReplacementGrades()
            && $first->getSubject() === $second->getSubject()
            && $first->getReplacementSubject() === $second->getReplacementSubject()
            && $first->getTeacher() === $second->getTeacher()
            && $first->getReplacementTeacher() === $second->getReplacementTeacher()
            && $first->getFlags() === $second->getFlags()
            && (($first->getType() === null && $second->getType() === null) || $first->getType() === $second->getType())
            && $first->getRemark() === $second->getRemark();
    }
}