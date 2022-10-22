<?php

namespace App\Timetable;

use Closure;
use App\Entity\Subject;
use App\Entity\TimetableLesson as TimetableLessonEntity;

/**
 * This helper filters lessons which are allowed to be visible due to their subjects.
 */
class TimetableFilter {

    /**
     * @param TimetableLessonEntity[] $lessons
     * @return array
     */
    private function filter(array $lessons, Closure $predicate) {
        $result = [ ];

        foreach($lessons as $lesson) {
            if($lesson->getTuition() !== null && $lesson->getTuition()->getSubject() !== null) {
                if($predicate($lesson->getTuition()->getSubject())) {
                    $result[] = $lesson;
                }
            } else if($lesson->getSubject() !== null) {
                if($predicate($lesson->getSubject()) === true) {
                    $result[] = $lesson;
                }
            } else {
                // Subject not provided
                $result[] = $lesson;
            }
        }

        return $result;
    }

    public function filterTeacherLessons(array $lessons) {
        return $this->filter($lessons, fn(Subject $subject) => $subject->isVisibleTeachers());
    }

    public function filterStudentLessons(array $lessons) {
        return $this->filter($lessons, fn(Subject $subject) => $subject->isVisibleStudents());
    }

    public function filterGradeLessons(array $lessons) {
        return $this->filter($lessons, fn(Subject $subject) => $subject->isVisibleGrades());
    }

    public function filterRoomLessons(array $lessons) {
        return $this->filter($lessons, fn(Subject $subject) => $subject->isVisibleRooms());
    }

    public function filterSubjectsLessons(array $lessons) {
        return $this->filter($lessons, fn(Subject $subject) => $subject->isVisibleSubjects());
    }
}