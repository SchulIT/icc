<?php

namespace App\Untis\Html;

class AbsentTeachersInfotextReader extends AbsentObjectiveInfotextReader {

    public function canHandle(?string $identifier): bool {
        return $identifier === 'Abwesende Lehrer';
    }

    protected function createAbsence(string $objective, ?int $lessonStart, ?int $lessonEnd): HtmlAbsence {
        return new HtmlAbsence(HtmlAbsenceObjectiveType::Teacher(), $objective, $lessonStart, $lessonEnd);
    }
}