<?php

namespace App\Untis\Html;

class AbsentStudyGroupsInfotextReader extends AbsentObjectiveInfotextReader {

    public function canHandle(?string $identifier): bool {
        return $identifier === 'Abwesende Klassen';
    }

    protected function createAbsence(string $objective, ?int $lessonStart, ?int $lessonEnd): HtmlAbsence {
        return new HtmlAbsence(HtmlAbsenceObjectiveType::StudyGroup(), $objective, $lessonStart, $lessonEnd);
    }
}