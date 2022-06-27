<?php

namespace App\Untis\Html\Substitution;

abstract class AbsentObjectiveInfotextReader implements InfotextReaderInterface {

    public const ItemSeparator = ',';

    protected abstract function createAbsence(string $objective, ?int $lessonStart, ?int $lessonEnd): Absence;

    public function handle(SubstitutionResult $result, string $content): void {
        foreach(explode(self::ItemSeparator, $content) as $item) {
            $result->addAbsence($this->handleItem(trim($item)));
        }
    }

    private function handleItem($content): Absence {
        $posLeftBracket = strpos($content, '(');
        $posRightBracket = strpos($content, ')');

        $lessonStart = null;
        $lessonEnd = null;
        $objective = $content;

        if($posLeftBracket !== false && $posRightBracket !== false) {
            $bracket = trim(substr($content, $posLeftBracket + 1, $posRightBracket - $posLeftBracket - 1));
            $lessons = explode('-', $bracket);

            $objective = trim(substr($content, 0, $posLeftBracket));
            $lessonStart = intval($lessons[0]);
            $lessonEnd = intval($lessons[1]);
        }

        return $this->createAbsence($objective, $lessonStart, $lessonEnd);
    }
}