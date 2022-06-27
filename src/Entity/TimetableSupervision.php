<?php

namespace App\Entity;

use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 */
class TimetableSupervision {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string|null
     */
    private ?string $externalId;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotNull()
     * @var DateTime|null
     */
    private ?DateTime $date;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(0)
     * @var int
     */
    private int $lesson;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private bool $isBefore = true;

    /**
     * @ORM\ManyToOne(targetEntity="Teacher")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull()
     * @var Teacher|null
     */
    private ?Teacher $teacher;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private ?string $location;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string {
        return $this->externalId;
    }

    /**
     * @param string $externalId
     * @return TimetableSupervision
     */
    public function setExternalId(string $externalId): TimetableSupervision {
        $this->externalId = $externalId;
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
     * @return TimetableSupervision
     */
    public function setDate(?DateTime $date): TimetableSupervision {
        $this->date = $date;
        return $this;
    }

    /**
     * @return int
     */
    public function getLesson(): int {
        return $this->lesson;
    }

    /**
     * @param int $lesson
     * @return TimetableSupervision
     */
    public function setLesson(int $lesson): TimetableSupervision {
        $this->lesson = $lesson;
        return $this;
    }

    /**
     * @return bool
     */
    public function isBefore(): bool {
        return $this->isBefore;
    }

    /**
     * @param bool $isBefore
     * @return TimetableSupervision
     */
    public function setIsBefore(bool $isBefore): TimetableSupervision {
        $this->isBefore = $isBefore;
        return $this;
    }

    /**
     * @return Teacher
     */
    public function getTeacher(): Teacher {
        return $this->teacher;
    }

    /**
     * @param Teacher $teacher
     * @return TimetableSupervision
     */
    public function setTeacher(Teacher $teacher): TimetableSupervision {
        $this->teacher = $teacher;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocation(): string {
        return $this->location;
    }

    /**
     * @param string $location
     * @return TimetableSupervision
     */
    public function setLocation(string $location): TimetableSupervision {
        $this->location = $location;
        return $this;
    }
}
