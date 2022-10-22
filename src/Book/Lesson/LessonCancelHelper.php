<?php

namespace App\Book\Lesson;

use App\Entity\LessonEntry;
use App\Entity\TimetableLesson;
use App\Repository\TimetableLessonRepositoryInterface;

class LessonCancelHelper {
    public function __construct(private TimetableLessonRepositoryInterface $lessonRepository)
    {
    }

    public function cancelLesson(TimetableLesson $lesson, string $reason) {
        $tuition = $lesson->getTuition();

        if($lesson->getEntries()->count() === 0) {
            $entry = (new LessonEntry())
                ->setLesson($lesson)
                ->setTuition($tuition)
                ->setLessonStart($lesson->getLessonStart())
                ->setLessonEnd($lesson->getLessonEnd())
                ->setIsCancelled(true)
                ->setTeacher($tuition->getTeachers()->first())
                ->setSubject($tuition->getSubject())
                ->setCancelReason($reason);

            $lesson->getEntries()->add($entry);
            $this->lessonRepository->persist($lesson);
        } else {
            $lessonNumbers = range($lesson->getLessonStart(), $lesson->getLessonEnd());

            /** @var LessonEntry $entry */
            foreach ($lesson->getEntries() as $entry) {
                for ($lessonNumber = $entry->getLessonStart(); $lessonNumber <= $entry->getLessonEnd(); $lessonNumber++) {
                    if(($key = array_search($lessonNumber, $lessonNumbers)) !== false) {
                        unset($lessonNumbers[$key]);
                    }
                }
            }

            foreach ($lessonNumbers as $lessonNumber) {
                $entry = (new LessonEntry())
                    ->setLesson($lesson)
                    ->setTuition($tuition)
                    ->setLessonStart($lessonNumber)
                    ->setLessonEnd($lessonNumber)
                    ->setIsCancelled(true)
                    ->setTeacher($tuition->getTeachers()->first())
                    ->setSubject($tuition->getSubject())
                    ->setCancelReason($reason);

                $lesson->getEntries()->add($entry);
                $this->lessonRepository->persist($lesson);
            }
        }
    }
}