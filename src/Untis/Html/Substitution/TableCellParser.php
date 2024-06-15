<?php

namespace App\Untis\Html\Substitution;

use App\Settings\TimetableSettings;
use Exception;
use Symfony\Component\String\AbstractString;
use function Symfony\Component\String\u;

class TableCellParser {

    public const EmptyCellValues = [ '???', '---', '+' ];

    public function __construct(private readonly TimetableSettings $timetableSettings) {

    }

    public function parseIntegerColumn(string $value): int {
        return intval($value);
    }

    public function parseStringOrNullColumn(?string $value): ?string {
        if($value === null) {
            return null;
        }

        $value = u($value);

        if($value->startsWith('(') && $value->endsWith(')')) {
            $value = $value->trimStart('(')->trimEnd(')');
        }

        if(in_array($value->toString(), self::EmptyCellValues)) {
            return null;
        }

        $value = $value->trim();

        if($value->isEmpty()) {
            return null;
        }

        return $value->toString();
    }

    public function parseMultiStringColumn(?string $value): array {
        if($value === null) {
            return [ ];
        }

        $value = u($value);

        if($value->startsWith('(') && $value->endsWith(')')) {
            $value = $value->trimStart('(')->trimEnd(')');
        }

        $parts = $value->split(',');
        $parts = array_map(fn(AbstractString $string) => $this->parseStringOrNullColumn($string->toString()), $parts);

        return array_filter($parts, fn(?string $value) => !empty($value));
    }

    /**
     * @throws Exception
     */
    public function parseLessonColumn(string $value): ParsedLesson {
        $value = trim($value);
        $isBefore = false;

        // Hack: if no lessons are provided, set all lessons
        if(empty($value)) {
            return new ParsedLesson(1, $this->timetableSettings->getMaxLessons(), false);
        }

        // Case 1: single lesson
        if(is_numeric($value)) {
            $lesson = intval($value);
            return new ParsedLesson($lesson, $lesson, false);
        }

        // Case 2: compound lessons (X-Y)
        if(str_contains($value, '-') && count($lessons = explode('-', $value)) === 2) {
            return new ParsedLesson(intval($lessons[0]), intval($lessons[1]), false);
        }

        // Case 3: Supervisions (X/Y)
        if(str_contains($value, '/') && count($lessons = explode('/', $value)) === 2) {
            return new ParsedLesson(intval($lessons[0]), intval($lessons[1]), true);
        }

        throw new Exception(sprintf('Spalte "Stunde" konnte aufgrund eines ungültiges Formates nicht eingelesen werden: "%s" eingelesen.', $value));
    }

    public function getCellIndexOrNull(array $idxes, ?string $columnName): ?int {
        if($columnName === null) {
            return null;
        }

        return $idxes[$columnName] ?? null;
    }
}