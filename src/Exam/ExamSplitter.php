<?php

namespace App\Exam;

use App\Entity\Exam;
use App\Entity\ExamStudent;
use App\Sorting\ExamStudentStrategy;
use App\Sorting\Sorter;

class ExamSplitter {

    public function __construct(private readonly Sorter $sorter) {

    }

    public function persistExamSplitResult(Exam $exam, ExamSplitResult $result): void {

    }

    public function split(Exam $exam, ExamSplitConfiguration $configuration): ExamSplitResult {
        if(!empty($exam->getExternalId())) {
            return new ExamSplitResult([], $exam->getStudents()->toArray());
        }

        /** @var ExamStudent[] $examStudents */
        $examStudents = $exam->getStudents()->toArray();
        $this->sorter->sort($examStudents, ExamStudentStrategy::class);

        $resultingExams = [ ];

        foreach($configuration->splits as $examSplit) {
            $newExam = $this->cloneExam($exam);
            $newExam->setDescription($examSplit->description);
            $newExam->setRoom($examSplit->room);

            $mustAdd = false;
            $clonedExamStudents = $examStudents;
            foreach($clonedExamStudents as $examStudent) {
                if($examStudent->getStudent()->getId() === $examSplit->firstStudent->getId()) {
                    $mustAdd = true;
                }

                if($mustAdd === true) {
                    $newExam->addStudent(
                        (new ExamStudent())
                            ->setStudent($examStudent->getStudent())
                            ->setTuition($examStudent->getTuition())
                    );

                    // Remove from existing exam students list
                    $existingKey = array_search($examStudent, $examStudents);
                    if($existingKey !== false) {
                        unset($examStudents[$existingKey]);
                    }
                }

                if($examStudent->getStudent()->getId() === $examSplit->lastStudent->getId()) {
                    $mustAdd = false;
                    break;
                }
            }

            $resultingExams[] = $newExam;
        }

        return new ExamSplitResult($resultingExams, $examStudents);
    }

    private function cloneExam(Exam $exam): Exam {
        $newExam = (new Exam())
            ->setLessonStart($exam->getLessonStart())
            ->setLessonEnd($exam->getLessonEnd())
            ->setDate($exam->getDate())
            ->setTuitionTeachersCanEditExam($exam->isTuitionTeachersCanEditExam());

        foreach($exam->getTuitions() as $tuition) {
            $newExam->addTuition($tuition);
        }

        return $newExam;
    }
}