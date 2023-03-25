<?php

namespace App\Book\Export;

class TuitionGrade {

    private string $gradeCategory;

    private string $student;

    private ?string $encryptedGrade;

    /**
     * @return string
     */
    public function getGradeCategory(): string {
        return $this->gradeCategory;
    }

    /**
     * @param string $gradeCategory
     * @return TuitionGrade
     */
    public function setGradeCategory(string $gradeCategory): TuitionGrade {
        $this->gradeCategory = $gradeCategory;
        return $this;
    }

    /**
     * @return string
     */
    public function getStudent(): string {
        return $this->student;
    }

    /**
     * @param string $student
     * @return TuitionGrade
     */
    public function setStudent(string $student): TuitionGrade {
        $this->student = $student;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEncryptedGrade(): ?string {
        return $this->encryptedGrade;
    }

    /**
     * @param string|null $encryptedGrade
     * @return TuitionGrade
     */
    public function setEncryptedGrade(?string $encryptedGrade): TuitionGrade {
        $this->encryptedGrade = $encryptedGrade;
        return $this;
    }
}