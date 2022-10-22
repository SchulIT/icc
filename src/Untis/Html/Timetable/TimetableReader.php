<?php

namespace App\Untis\Html\Timetable;

use App\Untis\Html\AbstractHtmlReader;
use App\Untis\Html\HtmlParseException;
use DOMNode;
use DOMXPath;
use InvalidArgumentException;

class TimetableReader extends AbstractHtmlReader {
    private const ObjectiveSelector = '/html/body/center/font[3]';
    private const TableSelector = "//table[@rules='all']";
    private const LessonIndicatorCellSelector = './tr[position()>1]/td[1]'; // First tr is the header

    private const FirstLesson = 1;

    private array $gradeCellInformation;
    private array $subjectCellInformation;

    public function __construct() {
        $this->gradeCellInformation = [
            CellInformationType::Weeks(), CellInformationType::Subject(), CellInformationType::Teacher(), CellInformationType::Room()
        ];

        $this->subjectCellInformation = [
            CellInformationType::Weeks(), CellInformationType::Teacher(), CellInformationType::Room()
        ];
    }

    /**
     * @throws HtmlParseException
     */
    public function readHtml(string $html, TimetableType $type): TimetableResult {
        $xpath = $this->getXPath($html);
        $objective = $this->parseObjective($xpath);
        $lessons = $this->parseLessons($xpath, $type);

        if($type->equals(TimetableType::Grade())) {
            foreach($lessons as $lesson) {
                $lesson->setGrade($objective);
            }
        } else if($type->equals(TimetableType::Subject())) {
            foreach($lessons as $lesson) {
                $lesson->setSubject($objective);
            }
        }

        return new TimetableResult($objective, $lessons);
    }

    /**
     * @throws HtmlParseException
     */
    private function parseObjective(DOMXPath $xpath): string {
        $elements = $xpath->query(self::ObjectiveSelector);
        $firstElement = $elements !== false ? $elements->item(0) : null;

        if($firstElement === null) {
            throw new HtmlParseException('XPath for objective failed.');
        }

        return trim($firstElement->nodeValue);
    }

    /**
     * @return Lesson[]
     * @throws HtmlParseException
     */
    private function parseLessons(DOMXPath $xpath, TimetableType $type): array {
        $lessons = [ ];

        $table = $xpath->query(self::TableSelector);
        $table = $table !== false ? $table->item(0) : null;

        if($table === null) {
            throw new HtmlParseException('XPath for lessons table failed.');
        }

        $numberOfLessons = $this->getNumberOfLessons($xpath, $table);
        $lessonStarts = $this->computeLessonStarts($xpath, $table, $numberOfLessons);

        $trNodes = $xpath->query('./tr', $table);
        $currentLesson = 0;

        $cellTypes = $type->equals(TimetableType::Grade()) ? $this->gradeCellInformation : $this->subjectCellInformation;

        for($idx = 1; $idx < $trNodes->count(); $idx++) {
            $trNode = $trNodes->item($idx);
            $tdNodes = $xpath->query('./td', $trNode);

            if($tdNodes === false || $tdNodes->count() === 0) {
                // Every second row is empty/has no child
                continue;
            }

            $currentDay = 0;

            for($tdIdx = 1; $tdIdx < $tdNodes->count(); $tdIdx++) {
                $currentDay += $this->computeAdvanceDayCount($lessonStarts, $currentLesson, $currentDay);

                $lessonStart = $currentLesson;
                $numberOfLessonsStartingAtLessonStart = count((array) array_filter($lessonStarts[$currentDay], fn(int $start) => $start === $lessonStart));
                $lessonEnd = $currentLesson + $numberOfLessonsStartingAtLessonStart - 1;

                $lessons = array_merge($lessons, $this->parseLessonsFromCell($xpath, $tdNodes->item($tdIdx), $currentDay, $lessonStart, $lessonEnd, $cellTypes, true));
                $currentDay++;
            }

            $currentLesson++;
        }

        return $lessons;
    }

    /**
     * @throws HtmlParseException
     */
    private function getNumberOfLessons(DOMXPath $xpath, DOMNode $table): int {
        $tdNodes = $xpath->query(self::LessonIndicatorCellSelector, $table);

        if($tdNodes === false) {
            throw new HtmlParseException('XPath for getting number of lessons failed.');
        }

        return count($tdNodes);
    }

    /**
     * @throws HtmlParseException
     */
    private function computeLessonStarts(DOMXPath $xpath, DOMNode $table, int $numberOfLessons): array {
        $trNodes = $xpath->query('./tr', $table);
        $lessonStarts = [ ];

        for($day = 0; $day < 5; $day++) {
            $lessonStarts[$day] = range(0, $numberOfLessons - 1);
        }

        if($trNodes === false) {
            throw new HtmlParseException('XPath for getting trNodes failed.');
        }

        $lesson = 0;

        for($idx = 1; $idx < count($trNodes); $idx++) {
            $trNode = $trNodes->item($idx);
            $tdNodes = $xpath->query('./td', $trNode);

            if($tdNodes === false || $tdNodes->count() === 0) {
                // every second row is empty/has no child
                continue;
            }

            $day = 0;

            for($tdIdx = 1; $tdIdx < $tdNodes->count(); $tdIdx++) {
                $tdNode = $tdNodes->item($tdIdx);
                if($lessonStarts[$day][$lesson] == $lesson) {
                    $rowSpanAttribute = $tdNode->attributes->getNamedItem('rowspan');

                    if($rowSpanAttribute !== null) {
                        $rowSpan = intval($rowSpanAttribute->nodeValue);
                        $duration = $rowSpan / 2; // Untis needs two rows per lesson

                        for($currentLesson = $lesson + 1; $currentLesson < $lesson + $duration; $currentLesson++) {
                            $lessonStarts[$day][$currentLesson] = $lesson;
                        }
                    }
                }

                $day++;
            }

            $lesson++;
        }

        return $lessonStarts;
    }

    private function computeAdvanceDayCount(array $lessonStarts, int $lesson, int $day): int {
        if($day > count($lessonStarts)) {
            throw new InvalidArgumentException(sprintf('Parameter $day must be less than %d (%d given)', count($lessonStarts), $day));
        }

        if($lesson > (is_countable($lessonStarts[0]) ? count($lessonStarts[0]) : 0)) {
            throw new InvalidArgumentException(sprintf('Parameter $lesson mut be less than %d (%d given)', is_countable($lessonStarts[0]) ? count($lessonStarts[0]) : 0, $lesson));
        }

        $advance = 0;

        for($currentDay = $day; $currentDay < count($lessonStarts); $currentDay++) {
            if($lessonStarts[$currentDay][$lesson] != $lesson) {
                $advance++;
            } else {
                break;
            }
        }

        return $advance;
    }

    /**
     * @param CellInformationType[] $cellTypes
     * @return Lesson[]
     */
    private function parseLessonsFromCell(DOMXPath $xpath, DOMNode $tdNode, int $day, int $lessonStart, int $lessonEnd, array $cellTypes, bool $useWeeks = true): array {
        $lessons = [ ];

        $nodes = $xpath->query('./table/tr', $tdNode);

        if($nodes === false || $nodes->count() === 0) {
            return $lessons;
        }

        for($idx = 0; $idx < $nodes->count(); $idx++) {
            $lessonNode = $nodes->item($idx);
            $tdNodes = $xpath->query('./td', $lessonNode);

            if($tdNodes === false || $tdNodes->count() < 2) {
                continue;
            }

            $lesson = new Lesson();
            $lesson->setLessonStart($lessonStart + self::FirstLesson);
            $lesson->setLessonEnd($lessonEnd + self::FirstLesson);
            $lesson->setDay($day + 1);

            for($nodeIdx = 0; $nodeIdx < $tdNodes->count(); $nodeIdx++) {
                $value = trim($tdNodes->item($nodeIdx)->nodeValue);
                $property = $cellTypes[$useWeeks ? $nodeIdx : $nodeIdx + 1];

                if($property->equals(CellInformationType::Room())) {
                    $lesson->setRoom($value);
                } else if($property->equals(CellInformationType::Subject())) {
                    $lesson->setSubject($value);
                } else if($property->equals(CellInformationType::Teacher())) {
                    $lesson->setTeacher($value);
                } else if($property->equals(CellInformationType::Weeks())) {
                    $lesson->setWeeks(explode(',', $value));
                }
            }

            $lessons[] = $lesson;
        }

        return $lessons;
    }
}