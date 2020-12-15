<?php

namespace App\Entity;

use App\Validator\DateIsNotInPast;
use App\Validator\NoReservationCollision;
use DateTime;
use DH\DoctrineAuditBundle\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 * @NoReservationCollision(groups={"Default", "collision"})
 */
class RoomReservation {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Room")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull()
     * @var Room
     */
    private $room;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotNull()
     * @DateIsNotInPast()
     * @var DateTime
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(0)
     * @var int
     */
    private $lessonStart = 0;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThanOrEqual(propertyPath="lessonStart")
     * @var int
     */
    private $lessonEnd = 0;

    /**
     * @ORM\ManyToOne(targetEntity="Teacher")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull()
     * @var Teacher
     */
    private $teacher;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return Room|null
     */
    public function getRoom(): ?Room {
        return $this->room;
    }

    /**
     * @param Room|null $room
     * @return RoomReservation
     */
    public function setRoom(?Room $room): RoomReservation {
        $this->room = $room;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getDate(): ?DateTime {
        return $this->date;
    }

    /**
     * @param DateTime|null $date
     * @return RoomReservation
     */
    public function setDate(?DateTime $date): RoomReservation {
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
     * @return RoomReservation
     */
    public function setLessonStart(int $lessonStart): RoomReservation {
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
     * @return RoomReservation
     */
    public function setLessonEnd(int $lessonEnd): RoomReservation {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    /**
     * @return Teacher|null
     */
    public function getTeacher(): ?Teacher {
        return $this->teacher;
    }

    /**
     * @param Teacher|null $teacher
     * @return RoomReservation
     */
    public function setTeacher(?Teacher $teacher): RoomReservation {
        $this->teacher = $teacher;
        return $this;
    }

}