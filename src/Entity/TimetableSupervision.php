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
    private ?string $externalId = null;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime|null
     */
    #[Assert\NotNull]
    private ?DateTime $date = null;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    #[Assert\GreaterThan(0)]
    private int $lesson;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private bool $isBefore = true;

    /**
     * @ORM\ManyToOne(targetEntity="Teacher")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Teacher|null
     */
    #[Assert\NotNull]
    private ?Teacher $teacher = null;

    /**
     * @ORM\Column(type="string")
     * @var string|null
     */
    #[Assert\NotBlank]
    private ?string $location = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getExternalId(): ?string {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): TimetableSupervision {
        $this->externalId = $externalId;
        return $this;
    }

    public function getDate(): ?DateTime {
        return $this->date;
    }

    public function setDate(?DateTime $date): TimetableSupervision {
        $this->date = $date;
        return $this;
    }

    public function getLesson(): int {
        return $this->lesson;
    }

    public function setLesson(int $lesson): TimetableSupervision {
        $this->lesson = $lesson;
        return $this;
    }

    public function isBefore(): bool {
        return $this->isBefore;
    }

    public function setIsBefore(bool $isBefore): TimetableSupervision {
        $this->isBefore = $isBefore;
        return $this;
    }

    public function getTeacher(): Teacher {
        return $this->teacher;
    }

    public function setTeacher(Teacher $teacher): TimetableSupervision {
        $this->teacher = $teacher;
        return $this;
    }

    public function getLocation(): string {
        return $this->location;
    }

    public function setLocation(string $location): TimetableSupervision {
        $this->location = $location;
        return $this;
    }
}
