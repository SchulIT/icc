<?php

namespace App\Untis\Html;

use Exception;
use Symfony\Component\String\AbstractString;
use function Symfony\Component\String\u;

class TableCellParser {

    public const EmptyCellValues = [ '???', '---', '+' ];

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
        $parts = array_map(function(AbstractString $string) {
            return $this->parseStringOrNullColumn($string->toString());
        }, $parts);

        return array_filter($parts, function(?string $value) {
            return !empty($value);
        });
    }

    /**
     * @throws Exception
     */
    public function parseLessonColumn(string $value): ParsedLesson {
        $value = trim($value);
        $isBefore = false;

        // Case 1: single lesson
        if(is_numeric($value)) {
            $lesson = intval($value);
            return new ParsedLesson($lesson, $lesson, false);
        }

        // Case 2: compound lessons (X-Y)
        if(strpos($value, '-') !== false && count($lessons = explode('-', $value)) === 2) {
            return new ParsedLesson(intval($lessons[0]), intval($lessons[1]), false);
        }

        // Case 3: Supervisions (X/Y)
        if(strpos($value, '/') !== false && count($lessons = explode('/', $value)) === 2) {
            return new ParsedLesson(intval($lessons[0]), intval($lessons[1]), true);
        }

        throw new Exception(sprintf('Error parsing lesson data: "%s" given', $value));
    }

    public function getCellIndexOrNull(array $idxes, ?string $columnName): ?int {
        if($columnName === null) {
            return null;
        }

        return $idxes[$columnName] ?? null;
    }
}