<?php

namespace App\Book\Export;

use DateTime;
use JMS\Serializer\Annotation as Serializer;

class Book {

    #[Serializer\Type(Section::class)]
    #[Serializer\SerializedName('section')]
    private ?Section $section = null;

    /**
     * @var Grade[]
     */
    #[Serializer\Type('array<App\Book\Export\Grade>')]
    #[Serializer\SerializedName('grades')]
    private array $grades = [];

    #[Serializer\Type(Tuition::class)]
    #[Serializer\SerializedName('tuition')]
    private ?Tuition $tuition = null;

    #[Serializer\Type('DateTime')]
    #[Serializer\SerializedName('start')]
    private ?DateTime $start = null;

    #[Serializer\Type('DateTime')]
    #[Serializer\SerializedName('end')]
    private ?DateTime $end = null;

    /**
     * @var StudentSummary[]
     */
    #[Serializer\Type('array<App\Book\Export\StudentSummary>')]
    #[Serializer\SerializedName('students_summary')]
    private ?array $studentSummaries = null;

    /**
     * @var StudentGrades[]
     */
    #[Serializer\Type('array<App\Book\Export\StudentGrades>')]
    #[Serializer\SerializedName('students_grades')]
    private array $studentGrades = [ ];

    #[Serializer\Type('array<App\Book\Export\Responsibility>')]
    #[Serializer\SerializedName('responsibilities')]
    private array $responsibilities = [ ];

    #[Serializer\Type('array<App\Book\Export\AttendanceFlag>')]
    #[Serializer\SerializedName('flags')]
    private array $flags = [ ];

    #[Serializer\Type('array<App\Book\Export\AdditionalStudentInformation>')]
    #[Serializer\SerializedName('additional_student_information')]
    private array $additionalStudentInformation = [ ];

    /**
     * @var Week[]
     */
    #[Serializer\Type('array<App\Book\Export\Week>')]
    #[Serializer\SerializedName('weeks')]
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

    /**
     * @return StudentGrades[]
     */
    public function getStudentGrades(): array {
        return $this->studentGrades;
    }

    public function addStudentGrades(StudentGrades $studentGrades): Book {
        $this->studentGrades[] = $studentGrades;
        return $this;
    }

    public function getResponsibilities(): array {
        return $this->responsibilities;
    }

    public function addResponsibility(Responsibility $responsibility): Book {
        $this->responsibilities[] = $responsibility;
        return $this;
    }

    public function getFlags(): array {
        return $this->flags;
    }

    public function addFlag(AttendanceFlag $flag): void {
        $this->flags[] = $flag;
    }

    public function getAdditionalStudentInformation(): array {
        return $this->additionalStudentInformation;
    }

    public function addAdditionalStudentInformation(AdditionalStudentInformation $information): Book {
        $this->additionalStudentInformation[] = $information;
        return $this;
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