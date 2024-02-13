<?php

namespace App\Validator;

use App\Entity\Exam;
use App\Entity\ExamStudent;
use App\Entity\Student;
use App\Entity\Tuition;
use App\Repository\ExamRepositoryInterface;
use App\Utils\ArrayUtils;
use Symfony\Component\Validator\ConstraintValidator;

abstract class AbstractExamConstraintValidator extends ConstraintValidator {
    private array $examCache = [ ];
    private bool $initialized = false;

    public function __construct(protected ExamRepositoryInterface $examRepository)
    {
    }

    protected function initializeCache(): void {
        if($this->initialized === true) {
            return;
        }

        $exams = $this->examRepository->findAll();

        foreach($exams as $exam) {
            /** @var ExamStudent $examStudent */
            foreach($exam->getStudents() as $examStudent) {
                $student = $examStudent->getStudent();

                if(!isset($this->examCache[$student->getId()])) {
                    $this->examCache[$student->getId()] = [ ];
                }

                $this->examCache[$student->getId()][] = $exam;
            }
        }

        $this->initialized = true;
    }

    protected function findAllByStudent(Student $student): array {
        $this->initializeCache();

        return $this->examCache[$student->getId()] ?? [ ];
    }


    /**
     * @param Student[] $students
     * @return Exam[]
     */
    protected function findAllByStudents(array $students): array {
        $this->initializeCache();

        $exams = [ ];

        foreach($students as $student) {
            if (isset($this->examCache[$student->getId()])) {
                $exams = array_merge($exams, $this->examCache[$student->getId()]);
            }
        }

        return ArrayUtils::unique($exams);
    }
}