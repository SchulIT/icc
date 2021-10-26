<?php

namespace App\Untis;

use DateTime;

class GpuExam {

    /**
     * @var int
     */
    private $id;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $text;

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var int
     */
    private $lessonStart;

    /**
     * @var int
     */
    private $lessonEnd;

    /**
     * @var string[]
     */
    private $subjects = [ ];

    /**
     * @var int[]
     */
    private $tuitions = [ ];

    /**
     * @var string[]
     */
    private $students = [ ];

    /**
     * @var string[]
     */
    private $supervisions = [ ];

    /**
     * @var string[]
     */
    private $rooms = [ ];

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     * @return GpuExam
     */
    public function setId(int $id): GpuExam {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return GpuExam
     */
    public function setName(?string $name): GpuExam {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string {
        return $this->text;
    }

    /**
     * @param string|null $text
     * @return GpuExam
     */
    public function setText(?string $text): GpuExam {
        $this->text = $text;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return GpuExam
     */
    public function setDate(DateTime $date): GpuExam {
        $this->date = $date;
        return $this;
    }

    /**
     * @return int
     */
    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    /**
     * @param int $lessonStart
     * @return GpuExam
     */
    public function setLessonStart(int $lessonStart): GpuExam {
        $this->lessonStart = $lessonStart;
        return $this;
    }

    /**
     * @return int
     */
    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }

    /**
     * @param int $lessonEnd
     * @return GpuExam
     */
    public function setLessonEnd(int $lessonEnd): GpuExam {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getSubjects(): array {
        return $this->subjects;
    }

    /**
     * @param string[] $subjects
     * @return GpuExam
     */
    public function setSubjects(array $subjects): GpuExam {
        $this->subjects = $subjects;
        return $this;
    }

    /**
     * @return int[]
     */
    public function getTuitions(): array {
        return $this->tuitions;
    }

    /**
     * @param int[] $tuitions
     * @return GpuExam
     */
    public function setTuitions(array $tuitions): GpuExam {
        $this->tuitions = $tuitions;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getStudents(): array {
        return $this->students;
    }

    /**
     * @param string[] $students
     * @return GpuExam
     */
    public function setStudents(array $students): GpuExam {
        $this->students = $students;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getSupervisions(): array {
        return $this->supervisions;
    }

    /**
     * @param string[] $supervisions
     * @return GpuExam
     */
    public function setSupervisions(array $supervisions): GpuExam {
        $this->supervisions = $supervisions;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getRooms(): array {
        return $this->rooms;
    }

    /**
     * @param string[] $rooms
     * @return GpuExam
     */
    public function setRooms(array $rooms): GpuExam {
        $this->rooms = $rooms;
        return $this;
    }
}