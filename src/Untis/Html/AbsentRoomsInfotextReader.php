<?php

namespace App\Untis\Html;

class AbsentRoomsInfotextReader extends AbsentObjectiveInfotextReader {
    public function canHandle(?string $identifier): bool {
        return $identifier === 'Blockierte Räume';
    }

    protected function createAbsence(string $objective, ?int $lessonStart, ?int $lessonEnd): HtmlAbsence {
        return new HtmlAbsence(HtmlAbsenceObjectiveType::Room(), $objective, $lessonStart, $lessonEnd);
    }
}