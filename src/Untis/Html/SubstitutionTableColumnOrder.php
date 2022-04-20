<?php

namespace App\Untis\Html;

class SubstitutionTableColumnOrder {

    private ?int $idColumn;

    private ?int $dateColumn;

    private ?int $lessonColumn;

    private ?int $gradesColumn;

    private ?int $replacementGradesColumn;

    private ?int $teachersColumn;

    private ?int $replacementTeachersColumn;

    private ?int $subjectColumn;

    private ?int $replacementSubjectColumn;

    private ?int $roomColumn;

    private ?int $replacementRoomColumn;

    private ?int $typeColumn;

    private ?int $remarkColumn;

    /**
     * @return int|null
     */
    public function getIdColumn(): ?int {
        return $this->idColumn;
    }

    /**
     * @param int|null $idColumn
     * @return SubstitutionTableColumnOrder
     */
    public function setIdColumn(?int $idColumn): SubstitutionTableColumnOrder {
        $this->idColumn = $idColumn;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDateColumn(): ?int {
        return $this->dateColumn;
    }

    /**
     * @param int|null $dateColumn
     * @return SubstitutionTableColumnOrder
     */
    public function setDateColumn(?int $dateColumn): SubstitutionTableColumnOrder {
        $this->dateColumn = $dateColumn;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getLessonColumn(): ?int {
        return $this->lessonColumn;
    }

    /**
     * @param int|null $lessonColumn
     * @return SubstitutionTableColumnOrder
     */
    public function setLessonColumn(?int $lessonColumn): SubstitutionTableColumnOrder {
        $this->lessonColumn = $lessonColumn;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getGradesColumn(): ?int {
        return $this->gradesColumn;
    }

    /**
     * @param int|null $gradesColumn
     * @return SubstitutionTableColumnOrder
     */
    public function setGradesColumn(?int $gradesColumn): SubstitutionTableColumnOrder {
        $this->gradesColumn = $gradesColumn;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getReplacementGradesColumn(): ?int {
        return $this->replacementGradesColumn;
    }

    /**
     * @param int|null $replacementGradesColumn
     * @return SubstitutionTableColumnOrder
     */
    public function setReplacementGradesColumn(?int $replacementGradesColumn): SubstitutionTableColumnOrder {
        $this->replacementGradesColumn = $replacementGradesColumn;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTeachersColumn(): ?int {
        return $this->teachersColumn;
    }

    /**
     * @param int|null $teachersColumn
     * @return SubstitutionTableColumnOrder
     */
    public function setTeachersColumn(?int $teachersColumn): SubstitutionTableColumnOrder {
        $this->teachersColumn = $teachersColumn;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getReplacementTeachersColumn(): ?int {
        return $this->replacementTeachersColumn;
    }

    /**
     * @param int|null $replacementTeachersColumn
     * @return SubstitutionTableColumnOrder
     */
    public function setReplacementTeachersColumn(?int $replacementTeachersColumn): SubstitutionTableColumnOrder {
        $this->replacementTeachersColumn = $replacementTeachersColumn;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSubjectColumn(): ?int {
        return $this->subjectColumn;
    }

    /**
     * @param int|null $subjectColumn
     * @return SubstitutionTableColumnOrder
     */
    public function setSubjectColumn(?int $subjectColumn): SubstitutionTableColumnOrder {
        $this->subjectColumn = $subjectColumn;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getReplacementSubjectColumn(): ?int {
        return $this->replacementSubjectColumn;
    }

    /**
     * @param int|null $replacementSubjectColumn
     * @return SubstitutionTableColumnOrder
     */
    public function setReplacementSubjectColumn(?int $replacementSubjectColumn): SubstitutionTableColumnOrder {
        $this->replacementSubjectColumn = $replacementSubjectColumn;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getRoomColumn(): ?int {
        return $this->roomColumn;
    }

    /**
     * @param int|null $roomColumn
     * @return SubstitutionTableColumnOrder
     */
    public function setRoomColumn(?int $roomColumn): SubstitutionTableColumnOrder {
        $this->roomColumn = $roomColumn;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getReplacementRoomColumn(): ?int {
        return $this->replacementRoomColumn;
    }

    /**
     * @param int|null $replacementRoomColumn
     * @return SubstitutionTableColumnOrder
     */
    public function setReplacementRoomColumn(?int $replacementRoomColumn): SubstitutionTableColumnOrder {
        $this->replacementRoomColumn = $replacementRoomColumn;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTypeColumn(): ?int {
        return $this->typeColumn;
    }

    /**
     * @param int|null $typeColumn
     * @return SubstitutionTableColumnOrder
     */
    public function setTypeColumn(?int $typeColumn): SubstitutionTableColumnOrder {
        $this->typeColumn = $typeColumn;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getRemarkColumn(): ?int {
        return $this->remarkColumn;
    }

    /**
     * @param int|null $remarkColumn
     * @return SubstitutionTableColumnOrder
     */
    public function setRemarkColumn(?int $remarkColumn): SubstitutionTableColumnOrder {
        $this->remarkColumn = $remarkColumn;
        return $this;
    }
}