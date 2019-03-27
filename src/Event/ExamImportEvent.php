<?php

namespace App\Event;

use App\Entity\Exam;
use Symfony\Component\EventDispatcher\Event;

class ExamImportEvent extends Event {

    /** @var Exam[] */
    private $exams;

    /**
     * @param Exam[] $exams
     */
    public function __construct(array $exams = [ ]) {
        $this->exams = $exams;
    }

    /**
     * @return Exam[]
     */
    public function getExams(): array {
        return $this->exams;
    }
}