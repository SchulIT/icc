<?php

namespace App\Request\Data;

use DateTime;
use App\Validator\NullOrNotBlank;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class ExamData {

    /**
     * Your ID which is used to update existing exams.
     *
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $id = null;

    /**
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:s'>")
     */
    #[Assert\NotNull]
    private ?DateTime $date = null;

    /**
     * @Serializer\Type("int")
     */
    #[Assert\GreaterThan(0)]
    private ?int $lessonStart = null;

    /**
     * @Serializer\Type("int")
     */
    #[Assert\GreaterThan(0)]
    #[Assert\GreaterThanOrEqual(propertyPath: 'lessonStart')]
    private ?int $lessonEnd = null;

    /**
     * Optional description of the exam
     *
     * @Serializer\Type("string")
     * @NullOrNotBlank()
     */
    private ?string $description = null;

    /**
     * List of external tuition which are related to this exam.
     *
     * @Serializer\Type("array<App\Request\Data\ExamTuition>")
     * @var ExamTuition[]
     */
    private ?array $tuitions = null;

    /**
     * List of external student IDs which attend this exam.
     *
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private ?array $students = null;

    /**
     * Acronyms of the teachers (their acronyms) which supervise the exam.
     *
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private ?array $supervisions = null;

    /**
     * List of rooms, in which the exam takes place.
     *
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private ?array $rooms = null;

    public function getId(): string {
        return $this->id;
    }

    public function setId(string $id): ExamData {
        $this->id = $id;
        return $this;
    }

    public function getDate(): DateTime {
        return $this->date;
    }

    public function setDate(DateTime $date): ExamData {
        $this->date = $date;
        return $this;
    }

    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    public function setLessonStart(int $lessonStart): ExamData {
        $this->lessonStart = $lessonStart;
        return $this;
    }

    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }

    public function setLessonEnd(int $lessonEnd): ExamData {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): ExamData {
        $this->description = $description;
        return $this;
    }

    /**
     * @return ExamTuition[]
     */
    public function getTuitions(): array {
        return $this->tuitions;
    }

    /**
     * @param ExamTuition[] $tuitions
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
     */
    public function setRooms(array $rooms): ExamData {
        $this->rooms = $rooms;
        return $this;
    }
}