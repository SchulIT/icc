<?php

namespace App\Untis\Html\Substitution;

class SubstitutionTableColumnOrder {

    private ?int $idColumn = null;

    private ?int $dateColumn = null;

    private ?int $lessonColumn = null;

    private ?int $gradesColumn = null;

    private ?int $replacementGradesColumn = null;

    private ?int $teachersColumn = null;

    private ?int $replacementTeachersColumn = null;

    private ?int $subjectColumn = null;

    private ?int $replacementSubjectColumn = null;

    private ?int $roomColumn = null;

    private ?int $replacementRoomColumn = null;

    private ?int $typeColumn = null;

    private ?int $remarkColumn = null;

    private ?int $isCancelledColumn = null;

    public function getIdColumn(): ?int {
        return $this->idColumn;
    }

    public function setIdColumn(?int $idColumn): SubstitutionTableColumnOrder {
        $this->idColumn = $idColumn;
        return $this;
    }

    public function getDateColumn(): ?int {
        return $this->dateColumn;
    }

    public function setDateColumn(?int $dateColumn): SubstitutionTableColumnOrder {
        $this->dateColumn = $dateColumn;
        return $this;
    }

    public function getLessonColumn(): ?int {
        return $this->lessonColumn;
    }

    public function setLessonColumn(?int $lessonColumn): SubstitutionTableColumnOrder {
        $this->lessonColumn = $lessonColumn;
        return $this;
    }

    public function getGradesColumn(): ?int {
        return $this->gradesColumn;
    }

    public function setGradesColumn(?int $gradesColumn): SubstitutionTableColumnOrder {
        $this->gradesColumn = $gradesColumn;
        return $this;
    }

    public function getReplacementGradesColumn(): ?int {
        return $this->replacementGradesColumn;
    }

    public function setReplacementGradesColumn(?int $replacementGradesColumn): SubstitutionTableColumnOrder {
        $this->replacementGradesColumn = $replacementGradesColumn;
        return $this;
    }

    public function getTeachersColumn(): ?int {
        return $this->teachersColumn;
    }

    public function setTeachersColumn(?int $teachersColumn): SubstitutionTableColumnOrder {
        $this->teachersColumn = $teachersColumn;
        return $this;
    }

    public function getReplacementTeachersColumn(): ?int {
        return $this->replacementTeachersColumn;
    }

    public function setReplacementTeachersColumn(?int $replacementTeachersColumn): SubstitutionTableColumnOrder {
        $this->replacementTeachersColumn = $replacementTeachersColumn;
        return $this;
    }

    public function getSubjectColumn(): ?int {
        return $this->subjectColumn;
    }

    public function setSubjectColumn(?int $subjectColumn): SubstitutionTableColumnOrder {
        $this->subjectColumn = $subjectColumn;
        return $this;
    }

    public function getReplacementSubjectColumn(): ?int {
        return $this->replacementSubjectColumn;
    }

    public function setReplacementSubjectColumn(?int $replacementSubjectColumn): SubstitutionTableColumnOrder {
        $this->replacementSubjectColumn = $replacementSubjectColumn;
        return $this;
    }

    public function getRoomColumn(): ?int {
        return $this->roomColumn;
    }

    public function setRoomColumn(?int $roomColumn): SubstitutionTableColumnOrder {
        $this->roomColumn = $roomColumn;
        return $this;
    }

    public function getReplacementRoomColumn(): ?int {
        return $this->replacementRoomColumn;
    }

    public function setReplacementRoomColumn(?int $replacementRoomColumn): SubstitutionTableColumnOrder {
        $this->replacementRoomColumn = $replacementRoomColumn;
        return $this;
    }

    public function getTypeColumn(): ?int {
        return $this->typeColumn;
    }

    public function setTypeColumn(?int $typeColumn): SubstitutionTableColumnOrder {
        $this->typeColumn = $typeColumn;
        return $this;
    }

    public function getRemarkColumn(): ?int {
        return $this->remarkColumn;
    }

    public function setRemarkColumn(?int $remarkColumn): SubstitutionTableColumnOrder {
        $this->remarkColumn = $remarkColumn;
        return $this;
    }

    public function getIsCancelledColumn(): ?int {
        return $this->isCancelledColumn;
    }

    public function setIsCancelledColumn(?int $isCancelledColumn): SubstitutionTableColumnOrder {
        $this->isCancelledColumn = $isCancelledColumn;
        return $this;
    }
}