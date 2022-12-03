<?php

namespace App\Book\Export;

use DateTime;
use JMS\Serializer\Annotation as Serializer;

class Book {

    /**
     * @Serializer\Type("App\Book\Export\Section")
     * @Serializer\SerializedName("section")
     */
    private ?Section $section = null;

    /**
     * @Serializer\Type("array<App\Book\Export\Grade>")
     * @Serializer\SerializedName("grades")
     * @var Grade[]
     */
    private array $grades = [];

    /**
     * @Serializer\Type("App\Book\Export\Tuition")
     * @Serializer\SerializedName("tuition")
     */
    private ?Tuition $tuition = null;

    /**
     * @Serializer\Type("DateTime")
     * @Serializer\SerializedName("start")
     */
    private ?DateTime $start = null;

    /**
     * @Serializer\Type("DateTime")
     * @Serializer\SerializedName("end")
     */
    private ?DateTime $end = null;

    /**
     * @Serializer\Type("array<App\Book\Export\StudentSummary>")
     * @Serializer\SerializedName("students_summary")
     * @var StudentSummary[]
     */
    private ?array $studentSummaries = null;

    /**
     * @Serializer\Type("array<App\Book\Export\Week>")
     * @Serializer\SerializedName("weeks")
     * @var Week[]
     */
    private ?array $weeks = null;

    public function getSection(): Section {
        return $this->section;
    }

    public function setSection(Section $section): Book {
        $this->section = $section;
        return $this;
    }

    /**
     * @param Grade[] $grades
     */
    public function setGrades(array $grades): Book {
        $this->grades = $grades;
        return $this;
    }

    /**
     * @return Grade[]
     */
    public function getGrades(): array {
        return $this->grades;
    }

    public function getTuition(): ?Tuition {
        return $this->tuition;
    }

    public function setTuition(?Tuition $tuition): Book {
        $this->tuition = $tuition;
        return $this;
    }

    public function getStart(): DateTime {
        return $this->start;
    }

    public function setStart(DateTime $start): Book {
        $this->start = $start;
        return $this;
    }

    public function getEnd(): DateTime {
        return $this->end;
    }

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