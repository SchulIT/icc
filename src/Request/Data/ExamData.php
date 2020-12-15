<?php

namespace App\Request\Data;

use App\Validator\NullOrNotBlank;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class ExamData {

    /**
     * Your ID which is used to update existing exams.
     *
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $id;

    /**
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:s'>")
     * @Assert\NotNull()
     * @var \DateTime
     */
    private $date;

    /**
     * @Serializer\Type("int")
     * @Assert\GreaterThan(0)
     * @var int
     */
    private $lessonStart;

    /**
     * @Serializer\Type("int")
     * @Assert\GreaterThan(0)
     * @Assert\GreaterThanOrEqual(propertyPath="lessonStart")
     * @var int
     */
    private $lessonEnd;

    /**
     * Optional description of the exam
     *
     * @Serializer\Type("string")
     * @NullOrNotBlank()
     * @var string|null
     */
    private $description;

    /**
     * List of external tuition IDs which are related to this exam.
     *
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private $tuitions;

    /**
     * List of external student IDs which attend this exam.
     *
     * @Serializer\Type("array<int>")
     * @var string[]
     */
    private $students;

    /**
     * Acronyms of the teachers (their acronyms) which supervise the exam.
     *
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private $supervisions;

    /**
     * List of rooms, in which the exam takes place.
     *
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private $rooms;

    /**
     * @return string
     */
    public function getId(): string {
        return $this->id;
    }

    /**
     * @param string $id
     * @return ExamData
     */
    public function setId(string $id): ExamData {
        $this->id = $id;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return ExamData
     */
    public function setDate(\DateTime $date): ExamData {
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
     * @return ExamData
     */
    public function setLessonStart(int $lessonStart): ExamData {
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
     * @return ExamData
     */
    public function setLessonEnd(int $lessonEnd): ExamData {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return ExamData
     */
    public function setDescription(?string $description): ExamData {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getTuitions(): array {
        return $this->tuitions;
    }

    /**
     * @param string[] $tuitions
     * @return ExamData
     */
    public function setTuitions(array $tuitions): ExamData {
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
     * @return ExamData
     */
    public function setStudents(array $students): ExamData {
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
     * @return ExamData
     */
    public function setSupervisions(array $supervisions): ExamData {
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
     * @return ExamData
     */
    public function setRooms(array $rooms): ExamData {
        $this->rooms = $rooms;
        return $this;
    }
}