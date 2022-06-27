<?php

namespace App\Untis\Html\Substitution;

class AbsentTeachersInfotextReader extends AbsentObjectiveInfotextReader {

    public function canHandle(?string $identifier): bool {
        return $identifier === 'Abwesende Lehrer';
    }

    protected function createAbsence(string $objective, ?int $lessonStart, ?int $lessonEnd): Absence {
        return new Absence(AbsenceObjectiveType::Teacher(), $objective, $lessonStart, $lessonEnd);
    }
}