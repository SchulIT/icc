<?php

namespace App\Entity;

use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[ORM\Entity]
class TimetableSupervision {

    use IdTrait;
    use UuidTrait;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', unique: true)]
    private ?string $externalId = null;

    /**
     * @var DateTime|null
     */
    #[Assert\NotNull]
    #[ORM\Column(type: 'datetime')]
    private ?DateTime $date = null;

    /**
     * @var int
     */
    #[Assert\GreaterThan(0)]
    #[ORM\Column(type: 'integer')]
    private int $lesson;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    private bool $isBefore = true;

    /**
     * @var Teacher|null
     */
    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Teacher::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Teacher $teacher = null;

    /**
     * @var string|null
     */
    #[Assert\NotBlank]
    #[ORM\Column(type: 'string')]
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
