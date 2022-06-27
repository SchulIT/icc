<?php

namespace App\Untis\Database\Timetable;

use App\Import\Importer;
use App\Import\ImportResult;
use App\Import\TimetableLessonsImportStrategy;
use App\Request\Data\TimetableLessonData;
use App\Request\Data\TimetableLessonsData;
use App\Untis\Database\Timetable\TimetableLesson;
use App\Untis\Database\Timetable\TimetableReader;
use DateTime;
use League\Csv\Reader;
use Ramsey\Uuid\Uuid;

class TimetableImporter {

    private Importer $importer;
    private TimetableLessonsImportStrategy $strategy;
    private TimetableReader $reader;

    public function __construct(Importer $importer, TimetableLessonsImportStrategy $strategy, TimetableReader $reader) {
        $this->importer = $importer;
        $this->strategy = $strategy;
        $this->reader = $reader;
    }

    public function import(Reader $reader, DateTime $start, DateTime $end): ImportResult {
        $data = new TimetableLessonsData();
        $data->setStartDate($start);
        $data->setEndDate($end);

        $raw = $this->reader->readDatabase($reader);
        $groups = $this->groupByTuitionNumber($raw);

        $lessons = [ ];

        $firstWeek = (int)$start->format('W');
        $firstWeekYear = (int)$start->format('Y');

        foreach($groups as $group) {
            $processed = [ ];

            foreach($group as $currentLesson) {
                if(in_array($currentLesson, $processed)) {
                    continue;
                }

                $processed[] = $currentLesson;

                $teachers = [ $currentLesson->getTeacher() ];
                $grades = [ $currentLesson->getGrade() ];
                $lessonStart = $currentLesson->getLesson();
                $lessonEnd = $currentLesson->getLesson();

                foreach($group as $compareLesson) {
                    if(in_array($compareLesson, $processed)) {
                        continue;
                    }

                    if($this->areAlmostIdentical($currentLesson, $compareLesson)) {
                        $teachers[] = $compareLesson->getTeacher();
                        $grades[] = $compareLesson->getGrade();
                        $lessonEnd = max($currentLesson->getLesson(), $compareLesson->getLesson());
                        $processed[] = $compareLesson;
                    }
                }

                $date = (new DateTime())
                    ->setTime(0,0,0)
                    ->setISODate($firstWeekYear, $firstWeek);

                if($currentLesson->getDay() != 1) {
                    $date = $date->modify(sprintf('+%d days', $currentLesson->getDay() - 1));
                }

                while($date < $end) {
                    if(in_array((int)$date->format('W'), $currentLesson->getWeeks())) {
                        $lessons[] = (new TimetableLessonData())
                            ->setId(Uuid::uuid4()->toString())
                            ->setDate(clone $date)
                            ->setGrades(array_unique($grades))
                            ->setRoom($currentLesson->getRoom())
                            ->setTeachers(array_unique($teachers))
                            ->setSubject($currentLesson->getSubject())
                            ->setLessonStart($lessonStart)
                            ->setLessonEnd($lessonEnd);
                    }

                    $date = $date->modify('+7 days');
                }
            }
        }

        $data->setLessons($lessons);

        return $this->importer->replaceImport($data, $this->strategy);
    }

    /**
     * @param TimetableLesson[] $raw
     * @return TimetableLesson[][]
     */
    private function groupByTuitionNumber(array $raw): array {
        $groups = [ ];

        foreach($raw as $lesson) {
            if($lesson->getTuitionNumber() === null) {
                continue;
            }

            if(!array_key_exists($lesson->getTuitionNumber(), $groups)) {
                $groups[$lesson->getTuitionNumber()] = [ ];
            }

            $groups[$lesson->getTuitionNumber()][] = $lesson;
        }

        return $groups;
    }

    private function areAlmostIdentical(TimetableLesson $lessonA, TimetableLesson $lessonB): bool {
        return $lessonA->getDay() === $lessonB->getDay()
            && $lessonA->getSubject() === $lessonB->getSubject()
            && $lessonA->getRoom() === $lessonB->getRoom()
            && $lessonA->getWeeks() == $lessonB->getWeeks()
            && $lessonA->getTuitionNumber() === $lessonB->getTuitionNumber();
    }
}