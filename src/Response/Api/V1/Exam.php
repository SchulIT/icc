<?php

namespace App\Response\Api\V1;

use App\Entity\Exam as ExamEntity;
use App\Entity\ExamInvigilator;
use App\Entity\Tuition as TuitionEntity;
use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\UuidInterface;

class Exam {

    use UuidTrait;

    /**
     * @Serializer\SerializedName("date")
     * @Serializer\Type("DateTime")
     * @var DateTime
     */
    private $date;

    /**
     * @Serializer\SerializedName("lesson_start")
     * @Serializer\Type("integer")
     * @var int
     */
    private $lessonStart;

    /**
     * @Serializer\SerializedName("lesson_end")
     * @Serializer\Type("integer")
     * @var int
     */
    private $lessonEnd;

    /**
     * @Serializer\SerializedName("description")
     * @Serializer\Type("string")
     *
     * @var string|null
     */
    private $description;

    /**
     * @Serializer\SerializedName("tuitions")
     * @Serializer\Type("array<App\Response\Api\V1\Tuition>")
     * @var Tuition[]
     */
    private $tuitions;

    /**
     * @Serializer\SerializedName("supervisions")
     * @Serializer\Type("array<App\Response\Api\V1\ExamSupervision>")
     *
     * @var ExamSupervision[]
     */
    private $supervisions;

    /**
     * @Serializer\SerializedName("rooms")
     * @Serializer\Type("array<string>")
     *
     * @var string[]
     */
    private $rooms;

    /**
     * @return UuidInterface
     */
    public function getUuid(): UuidInterface {
        return $this->uuid;
    }

    /**
     * @param UuidInterface $uuid
     * @return Exam
     */
    public function setUuid(UuidInterface $uuid): Exam {
        $this->uuid = $uuid;
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
     * @return string|null
     */
    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return Exam
     */
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
     * @return Exam
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

    public static function fromEntity(ExamEntity $exam, array $supervisions): self {
        return (new static())
            ->setUuid($exam->getUuid())
            ->setDescription($exam->getDescription())
            ->setDate($exam->getDate())
            ->setLessonStart($exam->getLessonStart())
            ->setLessonEnd($exam->getLessonEnd())
            ->setRooms($exam->getRooms())
            ->setSupervisions(array_map(function(ExamInvigilator $supervisionEntity) {
                return ExamSupervision::fromEntity($supervisionEntity);
            }, $supervisions))
            ->setTuitions(array_map(function(TuitionEntity $tuitionEntity) {
                return Tuition::fromEntity($tuitionEntity);
            }, $exam->getTuitions()->toArray()));
    }
}