<?php

namespace App\Dashboard;

use App\Entity\Exam;
use App\Entity\Student;
use App\Entity\StudyGroupMembership;
use App\Entity\TimetableLesson;

class LessonViewItem extends AbstractViewItem {

    private $lesson;
    private $exams;

    /**
     * @param TimetableLesson $lesson
     * @param Exam[] $exams
     */
    public function __construct(TimetableLesson $lesson, array $exams) {
        $this->lesson = $lesson;
        $this->exams = $exams;
    }

    /**
     * @return TimetableLesson
     */
    public function getLesson(): TimetableLesson {
        return $this->lesson;
    }

    /**
     * Returns the affected students of an exam which takes place during this lesson
     *
     * @param Exam $exam
     * @return Student[]
     */
    public function getAffectedExamStudents(Exam $exam): array {
        if(!in_array($exam, $this->exams)) {
            throw new \InvalidArgumentException('Given exam must be in the exams array.');
        }

        $lessonStudents = $this->lesson
            ->getTuition()
            ->getStudyGroup()
            ->getMemberships()
            ->map(function (StudyGroupMembership $membership) {
                return $membership->getStudent();
            });

        $affectedStudents = [ ];

        foreach($exam->getStudents() as $examStudent) {
            foreach($lessonStudents as $lessonStudent) {
                if($examStudent->getId() === $lessonStudent->getId()) {
                    $affectedStudents[] = $examStudent;
                }
            }
        }

        return  $affectedStudents;
    }

    /**
     * @return Exam[]
     */
    public function getExams(): array {
        return $this->exams;
    }

    public function getBlockName(): string {
        return 'lesson';
    }
}