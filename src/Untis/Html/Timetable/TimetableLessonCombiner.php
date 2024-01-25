<?php

namespace App\Untis\Html\Timetable;

use App\Settings\TimetableSettings;
use App\Utils\ArrayUtils;
use Doctrine\Common\Collections\ArrayCollection;

class TimetableLessonCombiner {

    /**
     * @param Lesson[] $lessons
     * @return Lesson[]
     */
    public function combine(array $lessons): array {
        usort($lessons, function(Lesson $lessonA, Lesson $lessonB) {
            if ($lessonA->getDay() === $lessonB->getDay()) {
                return $lessonA->getLessonStart() <=> $lessonB->getLessonStart();
            }

            return $lessonA->getDay() < $lessonB->getDay() ? -1 : 1;
        });

        $result = [ ];
        $collection = new ArrayCollection($lessons); // for convenience only

        foreach($lessons as $lesson) {
            if(!$collection->contains($lesson)) {
                continue;
            }

            $lessonsToCompare = array_filter($lessons, fn(Lesson $compareLesson) => $this->isSameLesson($lesson, $compareLesson));

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
            && ArrayUtils::areEqual($lessonA->getWeeks(), $lessonB->getWeeks());
    }
}