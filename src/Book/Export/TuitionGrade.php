<?php

namespace App\Book\Export;

class TuitionGrade {

    private string $gradeCategory;

    private string $student;

    private ?string $encryptedGrade = null;

    public function getGradeCategory(): string {
        return $this->gradeCategory;
    }

    public function setGradeCategory(string $gradeCategory): TuitionGrade {
        $this->gradeCategory = $gradeCategory;
        return $this;
    }

    public function getStudent(): string {
        return $this->student;
    }

    public function setStudent(string $student): TuitionGrade {
        $this->student = $student;
        return $this;
    }

    public function getEncryptedGrade(): ?string {
        return $this->encryptedGrade;
    }

    public function setEncryptedGrade(?string $encryptedGrade): TuitionGrade {
        $this->encryptedGrade = $encryptedGrade;
        return $this;
    }
}