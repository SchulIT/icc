<?php

namespace App\Grouping;

use App\Entity\Appointment;
use App\Entity\Exam;
use App\Entity\Student;

class AbsentStudentGroup implements GroupInterface, SortableGroupInterface {

    /** @var Student[] */
    private $students;

    /**
     * @param Exam|Appointment|null $objective
     */
    public function __construct(private $objective)
    {
    }

    /**
     * @return Appointment|Exam|null
     */
    public function getObjective() {
        return $this->objective;
    }

    public function getKey() {
        return $this->objective;
    }

    public function addItem($item) {
        $this->students[] = $item;
    }

    public function &getItems(): array {
        return $this->students;
    }

    public function getStudents(): array {
        return $this->students;
    }

    public function isExam(): bool {
        return $this->objective instanceof Exam;
    }

    public function isAppointment(): bool {
        return $this->objective instanceof Appointment;
    }
}