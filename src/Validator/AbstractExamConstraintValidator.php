<?php

namespace App\Validator;

use App\Entity\Exam;
use App\Entity\Student;
use App\Entity\Tuition;
use App\Repository\ExamRepositoryInterface;
use App\Utils\ArrayUtils;
use Symfony\Component\Validator\ConstraintValidator;

abstract class AbstractExamConstraintValidator extends ConstraintValidator {
    private $examCache = [ ];
    private $initialized = false;

    protected $examRepository;

    public function __construct(ExamRepositoryInterface $examRepository) {
        $this->examRepository = $examRepository;
    }

    protected function initializeCache() {
        if($this->initialized === true) {
            return;
        }

        $exams = $this->examRepository->findAll();

        foreach($exams as $exam) {
            /** @var Student $student */
            foreach($exam->getStudents() as $student) {
                if(!isset($this->examCache[$student->getId()])) {
                    $this->examCache[$student->getId()] = [ ];
                }

                $this->examCache[$student->getId()][] = $exam;
            }
        }

        $this->initialized = true;
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