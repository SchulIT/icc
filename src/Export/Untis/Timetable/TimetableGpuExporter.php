<?php

namespace App\Export\Untis\Timetable;

use App\Csv\CsvHelper;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Repository\TimetableWeekRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

class TimetableGpuExporter {
    public function __construct(private readonly CsvHelper $csvHelper, private readonly TimetableLessonRepositoryInterface $timetableLessonRepository, private readonly TimetableWeekRepositoryInterface $weekRepository) {

    }

    public function generateCsvResponse(Configuration $configuration): Response {
        $rows = [ ];

        $alias = [ ];
        $weekMap = [ ];

        foreach($configuration->weeks as $week) {
            $alias[$week->week->getKey()] = $week->untisWeek;
        }

        foreach($this->weekRepository->findAll() as $week) {
            foreach($week->getWeeksAsIntArray() as $weekNumber) {
                $key = $week->getKey();

                if(isset($alias[$key])) {
                    $key = $alias[$key];
                }

                $weekMap[$weekNumber] = $key;
            }
        }

        $lessons = $this->timetableLessonRepository->findAllByRange($configuration->start, $configuration->end);
        foreach($lessons as $timetableLesson) {
            if($timetableLesson->getGrades()->count() === 0) {
                continue;
            }

            if($timetableLesson->getTeachers()->count() === 0) {
                continue;
            }

            for($lessonNumber = $timetableLesson->getLessonStart(); $lessonNumber <= $timetableLesson->getLessonEnd(); $lessonNumber++) {
                foreach($timetableLesson->getGrades() as $grade) {
                    foreach($timetableLesson->getTeachers() as $teacher) {
                        $row = [
                            $timetableLesson->getTuition()->getId(), // Feld 1
                            $grade->getName(), // Feld 2
                            $teacher->getAcronym(), // Feld 3
                            $timetableLesson->getSubjectName(), // Feld 4
                            $timetableLesson->getRoom()?->getName(), // Feld 5
                            $timetableLesson->getDate()->format('N'), // Feld 6
                            $lessonNumber, // Feld
                            null,   // Feld 8
                            $weekMap[intval($timetableLesson->getDate()->format('W'))]
                        ];

                        $rows[] = $row;
                    }
                }
            }
        }

        return $this->csvHelper->getCsvResponse(
            'GPU001.txt',
            $rows,
            ';'
        );
    }
}