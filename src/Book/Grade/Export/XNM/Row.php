<?php

namespace App\Book\Grade\Export\XNM;

use App\Entity\Student;
use App\Entity\Tuition;
use App\Entity\TuitionGrade;

class Row {
    public function __construct(private readonly Student $student, private readonly Tuition $tuition, private readonly TuitionGrade|null $grade,
                                private readonly string|null $kursArt, private readonly int|null $fehlstunden, private readonly int|null $fehlstundenUnentschuldigt, private readonly bool $istQualifikationsphase) {

    }

    public function getStudent(): Student {
        return $this->student;
    }

    public function getTuition(): Tuition {
        return $this->tuition;
    }

    public function getGrade(): TuitionGrade|null {
        return $this->grade;
    }

    public function getKursArt(): string|null {
        return $this->kursArt;
    }

    public function getFehlstunden(): ?int {
        return $this->fehlstunden;
    }

    public function getFehlstundenUnentschuldigt(): ?int {
        return $this->fehlstundenUnentschuldigt;
    }

    public function isIstQualifikationsphase(): bool {
        return $this->istQualifikationsphase;
    }
}