<?php

namespace App\Untis\Html\Substitution;

class AbsentRoomsInfotextReader extends AbsentObjectiveInfotextReader {
    public function canHandle(?string $identifier): bool {
        return $identifier === 'Blockierte Räume';
    }

    protected function createAbsence(string $objective, ?int $lessonStart, ?int $lessonEnd): Absence {
        return new Absence(AbsenceObjectiveType::Room, $objective, $lessonStart, $lessonEnd);
    }
}