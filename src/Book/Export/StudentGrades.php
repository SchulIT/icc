<?php

namespace App\Book\Export;

class StudentGrades {
    private array $categories;

    private array $grades;

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