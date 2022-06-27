<?php

namespace App\Untis\Gpu\Exam;

use DateTime;

class Exam {

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
     * @return Exam
     */
    public function setId(int $id): Exam {
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
     * @return Exam
     */
    public function setName(?string $name): Exam {
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
     * @return Exam
     */
    public function setText(?string $text): Exam {
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
     * @return Exam
     */
    public function setDate(DateTime $date): Exam {
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
     * @return Exam
     */
    public function setLessonStart(int $lessonStart): Exam {
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
     * @return Exam
     */
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
     * @return Exam
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
     * @return Exam
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
     * @return Exam
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
     * @return Exam
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
     * @return Exam
     */
    public function setRooms(array $rooms): Exam {
        $this->rooms = $rooms;
        return $this;
    }
}