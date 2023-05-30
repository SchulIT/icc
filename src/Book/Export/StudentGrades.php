<?php

namespace App\Book\Export;

class StudentGrades {
    private Tuition $tuition;

    private array $categories;

    private array $grades;

    public function getTuition(): Tuition {
        return $this->tuition;
    }

    public function setTuition(Tuition $tuition): StudentGrades {
        $this->tuition = $tuition;
        return $this;
    }

    public function addCategory(TuitionGradeCategory $category): self {
        $this->categories[] = $category;
        return $this;
    }

    /**
     * @return TuitionGradeCategory[]
     */
    public function getCategories(): array {
        return $this->categories;
    }

    /**
     * @return TuitionGrade[]
     */
    public function getGrades(): array {
        return $this->grades;
    }

    public function addGrade(TuitionGrade $grade): void {
        $this->grades[] = $grade;
    }
}