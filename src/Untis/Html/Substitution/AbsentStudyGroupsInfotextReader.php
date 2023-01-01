<?php

namespace App\Untis\Html\Substitution;

class AbsentStudyGroupsInfotextReader extends AbsentObjectiveInfotextReader {

    public function canHandle(?string $identifier): bool {
        return $identifier === 'Abwesende Klassen';
    }

    protected function createAbsence(string $objective, ?int $lessonStart, ?int $lessonEnd): Absence {
        return new Absence(AbsenceObjectiveType::StudyGroup, $objective, $lessonStart, $lessonEnd);
    }
}