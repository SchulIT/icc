<?php

namespace App\Untis\Html\Timetable;

use App\Grouping\Grouper;
use App\Import\Importer;
use App\Import\ImportException;
use App\Import\ImportResult;
use App\Import\TimetableLessonsImportStrategy;
use App\Repository\TimetableWeekRepositoryInterface;
use App\Request\Data\TimetableLessonData;
use App\Request\Data\TimetableLessonsData;
use App\Settings\UntisHtmlSettings;
use App\Settings\UntisSettings;
use App\Untis\Html\HtmlParseException;
use DateMalformedStringException;

readonly class TimetableImporter {
    public function __construct(private Importer  $importer, private TimetableLessonsImportStrategy $strategy, private TimetableReader $reader,
                                private TimetableWeekRepositoryInterface $weekRepository, private TimetableLessonCombiner $lessonCombiner,
                                private Grouper $grouper, private UntisSettings $settings, private UntisHtmlSettings $htmlSettings)
    {
    }

    /**
     * @param ImportRequest $importRequest
     * @return ImportResult
     * @throws HtmlParseException
     * @throws ImportException|DateMalformedStringException
     */
    public function import(ImportRequest $importRequest): ImportResult {
        $lessons = [ ];
        $countWeeks = count($this->weekRepository->findAll());

        foreach($importRequest->timetables as $timetable) {
            $tempLessons = [ ];

            foreach($timetable->gradeLessons as $html) {
                $result = $this->reader->readHtml($html, TimetableType::Grade);
                $tempLessons = array_merge(
                    $tempLessons,
                    $this->lessonCombiner->combine($result->getLessons())
                );
            }

            foreach($timetable->subjectLessons as $html) {
                $result = $this->reader->readHtml($html, TimetableType::Subject);
                $tempLessons = array_merge($tempLessons,
                    $this->lessonCombiner->combine($result->getLessons())
                );
            }

            $this->applyWeekOverrides($tempLessons);

            foreach($tempLessons as $lesson) {
                $lesson->setWeeks(
                    array_intersect($lesson->getWeeks(), $timetable->weeks)
                );
            }


            if($countWeeks > 1) {
                $tempLessons = array_filter($tempLessons, fn(Lesson $lesson) => count($lesson->getWeeks()) > 0); // just in case...
            }

            $lessons = array_merge($lessons, $tempLessons);
        }


        $groups = $this->grouper->group($lessons, LessonStrategy::class);

        $lessonsToImport = [ ];

        /**
         * This week map can be used to convert from calendar weeks to Untis weeks. It looks something like this:
         *
         * [ 1 => 'A', 2 => 'B', 3 => 'A', 4 => 'B', ...] (in case there are A/B-weeks)
         */
        $weeksMap = [ ];
        foreach($this->weekRepository->findAll() as $week) {
            foreach($week->getWeeksAsIntArray() as $weekNumber) {
                $weeksMap[$weekNumber] = $week->getKey();
            }
        }

        /**
         * This matrix is used to get a list of dates for a given week and day. It looks something like this:
         *
         * [
         *      'A' => [
         *          1 => [ '2022-01-03', '2022-01-17', ... ]
         *          2 => [ '2022-01-04', '2022-01-18', ... ]
         *      ],
         *      'B' => ...
         * ]
         */
        $weekMatrix = [ ];
        $current = clone $importRequest->start;
        while($current <= $importRequest->end) {
            $current->setTime(0, 0);

            $week = $weeksMap[(int)$current->format('W')];
            $day = (int)$current->format('w');

            if(!array_key_exists($week, $weekMatrix)) {
                $weekMatrix[$week] = [ ];
            }

            if(!array_key_exists($day, $weekMatrix[$week])) {
                $weekMatrix[$week][$day] = [ ];
            }

            $weekMatrix[$week][$day][] = $current;

            $current = (clone $current)->modify('+1 day');
        }

        $subjectOverrides = $this->getSubjectOverrideMap();

        /** @var LessonGroup $group */
        foreach($groups as $group) {
            if(count($group->getLessons()) === 0) {
                // just in case...
                continue;
            }

            $teachers = [ ];
            $grades = [ ];

            foreach($group->getLessons() as $lesson) {
                if(!in_array($lesson->getTeacher(), $teachers)) {
                    $teachers[] = $lesson->getTeacher();
                }

                if(!in_array($lesson->getGrade(), $grades)) {
                    $grades[] = $lesson->getGrade();
                }
            }

            $lesson = $group->getLessons()[0];

            // Iterate over weeks
            foreach($lesson->getWeeks() as $week) {
                if(!isset($weekMatrix[$week][$lesson->getDay()])) {
                    continue;
                }

                foreach($weekMatrix[$week][$lesson->getDay()] as $date) {
                    $lessonsToImport[] = (new TimetableLessonData())
                        ->setId($group->getKey() . '-' . $date->format('Y-m-d'))
                        ->setDate($date)
                        ->setGrades($grades)
                        ->setTeachers($teachers)
                        ->setRoom($lesson->getRoom())
                        ->setSubject($subjectOverrides[$lesson->getSubject()] ?? $this->recreateCourseName($lesson->getSubject()))
                        ->setLessonStart($lesson->getLessonStart())
                        ->setLessonEnd($lesson->getLessonEnd());
                }
            }
        }

        $data = new TimetableLessonsData();
        $data->setStartDate($importRequest->start);
        $data->setEndDate($importRequest->end);
        $data->setLessons($lessonsToImport);

        return $this->importer->replaceImport($data, $this->strategy);
    }

    private function recreateCourseName(?string $input): ?string {
        if($this->htmlSettings->getCourseNameBlockSize() === 0) {
            return $input;
        }

        if(!str_contains($input, ' ')) {
            return $input;
        }

        $blocks = explode(' ', $input);

        if(count($blocks) !== 2) {
            return $input;
        }

        $firstBlock = mb_strlen($blocks[0]);
        $secondBlock = mb_strlen($blocks[1]);
        $numberOfBlanks = $this->htmlSettings->getCourseNameBlockSize() - $firstBlock - $secondBlock;

        if($numberOfBlanks < 1) {
            return $input;
        }

        return $blocks[0] . str_repeat(' ', $numberOfBlanks) . $blocks[1];
    }

    /**
     * @param Lesson[] $lessons
     * @return void
     */
    private function applyWeekOverrides(array $lessons): void {
        $overrideMap = $this->getWeekOverrideMap();

        foreach($lessons as $lesson) {
            $weeks = $lesson->getWeeks();
            $newWeeks = [ ];

            foreach($weeks as $week) {
                if(!array_key_exists($week, $overrideMap)) {
                    $newWeeks[] = $week;
                } else {
                    $newWeeks = array_merge($newWeeks, $overrideMap[$week]);
                }
            }

            $lesson->setWeeks(array_unique($newWeeks));
        }
    }

    private function getWeekOverrideMap(): array {
        $map = [ ];

        foreach($this->settings->getTimetableWeekOverrides() as $override) {
            $map[$override['week']] = explode(',', $override['overrides']);
        }

        return $map;
    }

    private function getSubjectOverrideMap(): array {
        $map = [ ];

        foreach($this->settings->getSubjectOverrides() as $override) {
            $map[$override['untis']] = $override['override'];
        }

        return $map;
    }
}