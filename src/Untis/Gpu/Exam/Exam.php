<?php

namespace App\Untis\Gpu\Exam;

use DateTime;

class Exam {

    private ?int $id = null;

    private ?string $name = null;

    private ?string $text = null;

    private ?DateTime $date = null;

    private ?int $lessonStart = null;

    private ?int $lessonEnd = null;

    /**
     * @var string[]
     */
    private array $subjects = [ ];

    /**
     * @var int[]
     */
    private array $tuitions = [ ];

    /**
     * @var string[]
     */
    private array $students = [ ];

    /**
     * @var string[]
     */
    private array $supervisions = [ ];

    /**
     * @var string[]
     */
    private array $rooms = [ ];

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): Exam {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): Exam {
        $this->name = $name;
        return $this;
    }

    public function getText(): ?string {
        return $this->text;
    }

    public function setText(?string $text): Exam {
        $this->text = $text;
        return $this;
    }

    public function getDate(): DateTime {
        return $this->date;
    }

    public function setDate(DateTime $date): Exam {
        $this->date = $date;
        return $this;
    }

    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    public function setLessonStart(int $lessonStart): Exam {
        $this->lessonStart = $lessonStart;
        return $this;
    }

    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }

    public function setLessonEnd(int $lessonEnd): Exam {
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
     */
    public function setSubjects(array $subjects): Exam {
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
     */
    public function setTuitions(array $tuitions): Exam {
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
     */
    public function setStudents(array $students): Exam {
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
     */
    public function setSupervisions(array $supervisions): Exam {
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
     */
    public function setRooms(array $rooms): Exam {
        $this->rooms = $rooms;
        return $this;
    }
}