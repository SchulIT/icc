<?php

namespace App\Validator;

use App\Entity\Exam;
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
            /** @var Tuition $tuition */
            foreach($exam->getTuitions() as $tuition) {
                if(!isset($this->examCache[$tuition->getId()])) {
                    $this->examCache[$tuition->getId()] = [ ];
                }

                $this->examCache[$tuition->getId()][] = $exam;
            }
        }

        $this->initialized = true;
    }

    /**
     * @param Tuition[] $tuitions
     * @return Exam[]
     */
    protected function findAllByTuitions(array $tuitions): array {
        $this->initializeCache();

        $exams = [ ];

        foreach($tuitions as $tuition) {
            if(isset($this->examCache[$tuition->getId()])) {
                $exams = array_merge($exams, $this->examCache[$tuition->getId()]);
            }
        }

        return ArrayUtils::unique($exams);
    }
}