<?php

namespace App\Response\Api\V1;

use App\Entity\Exam as ExamEntity;
use App\Entity\ExamSupervision as ExamSupervisionEntity;
use App\Entity\Tuition as TuitionEntity;
use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\UuidInterface;

class Exam {

    use UuidTrait;

    /**
     * @Serializer\SerializedName("date")
     * @Serializer\Type("DateTime")
     */
    private ?\DateTime $date = null;

    /**
     * @Serializer\SerializedName("lesson_start")
     * @Serializer\Type("integer")
     */
    private ?int $lessonStart = null;

    /**
     * @Serializer\SerializedName("lesson_end")
     * @Serializer\Type("integer")
     */
    private ?int $lessonEnd = null;

    /**
     * @Serializer\SerializedName("description")
     * @Serializer\Type("string")
     */
    private ?string $description = null;

    /**
     * @Serializer\SerializedName("tuitions")
     * @Serializer\Type("array<App\Response\Api\V1\Tuition>")
     * @var Tuition[]
     */
    private ?array $tuitions = null;

    /**
     * @Serializer\SerializedName("supervisions")
     * @Serializer\Type("array<App\Response\Api\V1\ExamSupervision>")
     *
     * @var ExamSupervision[]
     */
    private ?array $supervisions = null;

    /**
     * @Serializer\SerializedName("rooms")
     * @Serializer\Type("array<string>")
     *
     * @var string[]
     */
    private ?array $rooms = null;

    public function getUuid(): UuidInterface {
        return $this->uuid;
    }

    public function setUuid(UuidInterface $uuid): Exam {
        $this->uuid = $uuid;
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

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): Exam {
        $this->description = $description;
        return $this;
    }

    /**
     * @return Tuition[]
     */
    public function getTuitions(): array {
        return $this->tuitions;
    }

    /**
     * @param Tuition[] $tuitions
     */
    public function setTuitions(array $tuitions): Exam {
        $this->tuitions = $tuitions;
        return $this;
    }

    /**
     * @return ExamSupervision[]
     */
    public function getSupervisions(): array {
        return $this->supervisions;
    }

    /**
     * @param ExamSupervision[] $supervisions
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

    public static function fromEntity(ExamEntity $exam, array $supervisions): self {
        return (new self())
            ->setUuid($exam->getUuid())
            ->setDescription($exam->getDescription())
            ->setDate($exam->getDate())
            ->setLessonStart($exam->getLessonStart())
            ->setLessonEnd($exam->getLessonEnd())
            ->setRooms($exam->getRoom() !== null ? [ $exam->getRoom()->getName() ] : [ ]) // for compatibility reasons only
            ->setSupervisions(array_map(fn(ExamSupervisionEntity $supervisionEntity) => ExamSupervision::fromEntity($supervisionEntity), $supervisions))
            ->setTuitions(array_map(fn(TuitionEntity $tuitionEntity) => Tuition::fromEntity($tuitionEntity), $exam->getTuitions()->toArray()));
    }
}