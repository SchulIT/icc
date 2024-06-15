<?php

namespace App\Book\Grade\Export\ZP10;

use App\Entity\Student;
use App\Entity\Subject;
use App\Entity\TuitionGrade;

class Row {
    public function __construct(private readonly Student $student, private Subject $subject, private readonly TuitionGrade|null $abschluss,
                                private readonly TuitionGrade|null $vornote, private readonly TuitionGrade|null $schriftlich, private readonly TuitionGrade|null $muendlich) {

    }

    public function getStudent(): Student {
        return $this->student;
    }

    public function getSubject(): Subject {
        return $this->subject;
    }

    /**
     * @return TuitionGrade|null
     */
    public function getAbschluss(): ?TuitionGrade {
        return $this->abschluss;
    }

    /**
     * @return TuitionGrade|null
     */
    public function getVornote(): ?TuitionGrade {
        return $this->vornote;
    }

    /**
     * @return TuitionGrade|null
     */
    public function getSchriftlich(): ?TuitionGrade {
        return $this->schriftlich;
    }

    /**
     * @return TuitionGrade|null
     */
    public function getMuendlich(): ?TuitionGrade {
        return $this->muendlich;
    }
}