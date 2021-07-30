<?php

namespace App\Book\Export;

use DateTime;
use JMS\Serializer\Annotation as Serializer;

class Book {

    /**
     * @Serializer\Type("App\Book\Export\Section")
     * @Serializer\SerializedName("section")
     * @var Section
     */
    private $section;

    /**
     * @Serializer\Type("App\Book\Export\Grade")
     * @Serializer\SerializedName("grade")
     * @var Grade|null
     */
    private $grade;

    /**
     * @Serializer\Type("App\Book\Export\Tuition")
     * @Serializer\SerializedName("tuition")
     * @var Tuition|null
     */
    private $tuition;

    /**
     * @Serializer\Type("DateTime")
     * @Serializer\SerializedName("start")
     * @var DateTime
     */
    private $start;

    /**
     * @Serializer\Type("DateTime")
     * @Serializer\SerializedName("end")
     * @var DateTime
     */
    private $end;

    /**
     * @Serializer\Type("array<App\Book\Export\StudentSummary>")
     * @Serializer\SerializedName("students_summary")
     * @var StudentSummary[]
     */
    private $studentSummaries;

    /**
     * @Serializer\Type("array<App\Book\Export\Week>")
     * @Serializer\SerializedName("weeks")
     * @var Week[]
     */
    private $weeks;

    /**
     * @return Section
     */
    public function getSection(): Section {
        return $this->section;
    }

    /**
     * @param Section $section
     * @return Book
     */
    public function setSection(Section $section): Book {
        $this->section = $section;
        return $this;
    }

    /**
     * @return Grade|null
     */
    public function getGrade(): ?Grade {
        return $this->grade;
    }

    /**
     * @param Grade|null $grade
     * @return Book
     */
    public function setGrade(?Grade $grade): Book {
        $this->grade = $grade;
        return $this;
    }

    /**
     * @return Tuition|null
     */
    public function getTuition(): ?Tuition {
        return $this->tuition;
    }

    /**
     * @param Tuition|null $tuition
     * @return Book
     */
    public function setTuition(?Tuition $tuition): Book {
        $this->tuition = $tuition;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStart(): DateTime {
        return $this->start;
    }

    /**
     * @param DateTime $start
     * @return Book
     */
    public function setStart(DateTime $start): Book {
        $this->start = $start;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEnd(): DateTime {
        return $this->end;
    }

    /**
     * @param DateTime $end
     * @return Book
     */
    public function setEnd(DateTime $end): Book {
        $this->end = $end;
        return $this;
    }

    public function addStudentSummary(StudentSummary $summary): void {
        $this->studentSummaries[] = $summary;
    }

    /**
     * @return StudentSummary[]
     */
    public function getStudentSummaries(): array {
        return $this->studentSummaries;
    }

    public function addWeek(Week $week): void {
        $this->weeks[] = $week;
    }

    /**
     * @return Week[]
     */
    public function getWeeks(): array {
        return $this->weeks;
    }
}