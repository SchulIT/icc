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
use App\Settings\UntisSettings;
use App\Untis\Html\HtmlParseException;
use DateTime;
use Symfony\Component\Stopwatch\Stopwatch;

class TimetableImporter {
    private Importer $importer;
    private TimetableLessonsImportStrategy $strategy;
    private TimetableReader $reader;
    private TimetableWeekRepositoryInterface $weekRepository;
    private Grouper $grouper;
    private UntisSettings $settings;
    private TimetableLessonCombiner $lessonCombiner;

    public function __construct(Importer $importer, TimetableLessonsImportStrategy $strategy, TimetableReader $reader,
                                TimetableWeekRepositoryInterface $weekRepository, TimetableLessonCombiner $combiner,
                                Grouper $grouper, UntisSettings $settings) {
        $this->importer = $importer;
        $this->strategy = $strategy;
        $this->reader = $reader;
        $this->weekRepository = $weekRepository;
        $this->lessonCombiner = $combiner;
        $this->grouper = $grouper;
        $this->settings = $settings;
    }

    /**
     * @param string[] $gradeLessonsHtml
     * @param string[] $subjectLessonsHtml
     * @param DateTime $start
     * @param DateTime $end
     * @return ImportResult
     * @throws HtmlParseException
     * @throws ImportException
     */
    public function import(array $gradeLessonsHtml, array $subjectLessonsHtml, DateTime $start, DateTime $end): ImportResult {
        $lessons = [ ];

        foreach($gradeLessonsHtml as $html) {
            $result = $this->reader->readHtml($html, TimetableType::Grade());
            $lessons = array_merge(
                $lessons,
                $this->lessonCombiner->combine($result->getLessons())
            );
        }

        foreach($subjectLessonsHtml as $html) {
            $result = $this->reader->readHtml($html, TimetableType::Subject());
            $lessons = array_merge($lessons, $result->getLessons());
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
        $current = clone $start;
        while($current <= $end) {
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
                        ->setSubject($subjectOverrides[$lesson->getSubject()] ?? $lesson->getSubject())
                        ->setLessonStart($lesson->getLessonStart())
                        ->setLessonEnd($lesson->getLessonEnd());
                }
            }
        }

        $data = new TimetableLessonsData();
        $data->setStartDate($start);
        $data->setEndDate($end);
        $data->setLessons($lessonsToImport);

        return $this->importer->replaceImport($data, $this->strategy);
    }

    private function getSubjectOverrideMap(): array {
        $map = [ ];

        foreach($this->settings->getSubjectOverrides() as $override) {
            $map[$override['untis']] = $override['override'];
        }

        return $map;
    }
}