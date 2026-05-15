<?php

namespace App\Exam;

use App\Exam\Entity\Exam;
use App\Exam\Entity\ExamStudent;
use App\Common\Entity\Section;
use App\Common\Entity\Student;
use App\Common\Entity\StudyGroupMembership;
use App\Exam\Repository\ExamRepositoryInterface;
use App\Common\Repository\TuitionRepositoryInterface;
use App\Framework\Utils\ArrayUtils;
use DateTime;

class ReassignmentsHelper {

    public function __construct(private readonly TuitionRepositoryInterface $tuitionRepository, private readonly ExamRepositoryInterface $examRepository) { }

    public function computeReassigns(Student $student, Section $section, DateTime $start): Reassignments {
        $tuitions = $this->tuitionRepository->findAllByStudents([$student], $section);

        /** @var Exam[] $oldExams */
        $oldExams = ArrayUtils::createArrayWithKeys(
            array_filter(
                $this->examRepository->findAllByStudents([$student]),
                fn(Exam $exam) => ($exam->getDate() === null || $exam->getDate() >= $start) && $this->isStudentPartOfExam($exam, $student)
            ),
            fn(Exam $exam) => $exam->getId()
        );

        /** @var Exam[] $newExams */
        $newExams = ArrayUtils::createArrayWithKeys(
            array_filter(
                $this->examRepository->findAllByTuitions(
                    $tuitions,
                    null,
                    false
                ),
                fn(Exam $exam) => ($exam->getDate() === null || $exam->getDate() >= $start) && $this->isStudentPartOfExam($exam, $student) !== true
            ),
            fn(Exam $exam) => $exam->getId()
        );

        $examsInBothArray = array_intersect_key($oldExams, $newExams);

        foreach($examsInBothArray as $examId => $exam) {
            unset($oldExams[$examId]);
            unset($newExams[$examId]);
        }

        /*
         * Fix case that student is already part of exam and still part of the tuition
         */
        $removeIds = [ ];
        foreach($tuitions as $tuition) {
            foreach($oldExams as $examId => $exam) {
                if($exam->getTuitions()->contains($tuition)) {
                    $removeIds[] = $examId;
                }
            }
        }

        $unchanged = [ ];
        foreach($removeIds as $id) {
            $unchanged[$id] = $oldExams[$id];
            unset($oldExams[$id]);
        }

        return new Reassignments($newExams, $oldExams, array_merge($examsInBothArray, $unchanged));
    }

    public function applyReassignment(Student $student, Reassignments $reassignments): void {
        $this->examRepository->beginTransaction();

        foreach ($reassignments->getExamsToRemove() as $exam) {
            $toRemove = $exam->getStudents()->filter(fn(ExamStudent $examStudent) => $examStudent->getStudent()->getId() === $student->getId())->first();

            if($toRemove !== null && $toRemove !== false) {
                $exam->removeStudent($toRemove);
                $this->examRepository->persist($exam);
            }
        }

        foreach($reassignments->getExamsToAdd() as $exam) {
            $toAdd = (new ExamStudent())
                ->setExam($exam)
                ->setStudent($student);

            $tuition = null;

            if($exam->getTuitions()->count() > 0) {
                foreach ($exam->getTuitions() as $examTuition) {
                    $possibleTuitions = $examTuition->getStudyGroup()->getMemberships()->filter(fn(StudyGroupMembership $membership) => $membership->getStudent()->getId() === $student->getId());
                    if($possibleTuitions->count() === 1) {
                        $tuition = $examTuition;
                    } else if($possibleTuitions->count() > 1) {
                        $tuition = null;
                    }
                }
            } else {
                $tuition = $exam->getTuitions()->first();
            }

            $toAdd->setTuition($tuition);

            $exam->addStudent($toAdd);
            $this->examRepository->persist($exam);
        }

        $this->examRepository->commit();
    }

    private function isStudentPartOfExam(Exam $exam, Student $student): bool {
        foreach($exam->getStudents() as $examStudent) {
            if($examStudent->getStudent()->getId() === $student->getId()) {
                return true;
            }
        }

        return false;
    }
}