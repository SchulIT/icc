<?php

namespace App\Untis\Html\Timetable;

use App\Settings\TimetableSettings;
use Doctrine\Common\Collections\ArrayCollection;

class TimetableLessonCombiner {

    /**
     * @param Lesson[] $lessons
     * @return Lesson[]
     */
    public function combine(array $lessons): array {
        usort($lessons, function(Lesson $lessonA, Lesson $lessonB) {
            if($lessonA->getDay() === $lessonB->getDay()) {
                if($lessonA->getLessonStart() === $lessonB->getLessonStart()) {
                    return 0;
                }

                return $lessonA->getLessonStart() < $lessonB->getLessonStart() ? -1 : 1;
            }

            return $lessonA->getDay() < $lessonB->getDay() ? -1 : 1;
        });

        $result = [ ];
        $collection = new ArrayCollection($lessons); // for convenience only

        foreach($lessons as $lesson) {
            if(!$collection->contains($lesson)) {
                continue;
            }

            $lessonsToCompare = array_filter($lessons, function(Lesson $compareLesson) use($lesson) {
                return $this->isSameLesson($lesson, $compareLesson);
            });

            foreach($lessonsToCompare as $compareLesson) {
                if($compareLesson->getLessonStart() === $lesson->getLessonEnd() + 1) {
                    $lesson->setLessonEnd($compareLesson->getLessonEnd());
                    $collection->removeElement($compareLesson);
                }
            }

            $collection->removeElement($lesson);
            $result[] = $lesson;
        }

        return $result;
    }

    private function isSameLesson(Lesson $lessonA, Lesson $lessonB): bool {
        return $lessonA->getGrade() === $lessonB->getGrade()
            && $lessonA->getDay() === $lessonB->getDay()
            && $lessonA->getRoom() === $lessonB->getRoom()
            && $lessonA->getSubject() === $lessonB->getSubject()
            && $lessonA->getTeacher() === $lessonB->getTeacher()
            && $lessonA->getWeeks() === $lessonB->getWeeks();
    }
}