<?php

namespace App\Entity;

use App\Validator\DateLessonGreaterThan;
use App\Validator\DateLessonInSection;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class TeacherAbsence {

    use IdTrait;
    use UuidTrait;

    #[ORM\ManyToOne(targetEntity: Teacher::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?Teacher $teacher;

    #[ORM\Embedded(class: DateLesson::class)]
    #[DateLessonInSection]
    private DateLesson $from;

    #[ORM\Embedded(class: DateLesson::class)]
    #[DateLessonGreaterThan(propertyPath: 'from')]
    #[DateLessonInSection]
    private DateLesson $until;

    #[ORM\ManyToOne(targetEntity: TeacherAbsenceType::class)]
    #[ORM\JoinColumn]
    #[Assert\NotNull]
    private ?TeacherAbsenceType $type;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $message;

    /**
     * @var Collection<TeacherAbsenceComment>
     */
    #[ORM\OneToMany(mappedBy: 'absence', targetEntity: TeacherAbsenceComment::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $comments;

    #[ORM\Column(type: 'datetime')]
    #[Timestampable(on: 'create')]
    private DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $processedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?User $processedBy = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->comments = new ArrayCollection();
    }

    /**
     * @return Teacher|null
     */
    public function getTeacher(): ?Teacher {
        return $this->teacher;
    }

    /**
     * @param Teacher|null $teacher
     * @return TeacherAbsence
     */
    public function setTeacher(?Teacher $teacher): TeacherAbsence {
        $this->teacher = $teacher;
        return $this;
    }

    /**
     * @return DateLesson
     */
    public function getFrom(): DateLesson {
        return $this->from;
    }

    /**
     * @param DateLesson $from
     * @return TeacherAbsence
     */
    public function setFrom(DateLesson $from): TeacherAbsence {
        $this->from = $from;
        return $this;
    }

    /**
     * @return DateLesson
     */
    public function getUntil(): DateLesson {
        return $this->until;
    }

    /**
     * @param DateLesson $until
     * @return TeacherAbsence
     */
    public function setUntil(DateLesson $until): TeacherAbsence {
        $this->until = $until;
        return $this;
    }

    /**
     * @return TeacherAbsenceType|null
     */
    public function getType(): ?TeacherAbsenceType {
        return $this->type;
    }

    /**
     * @param TeacherAbsenceType|null $type
     * @return TeacherAbsence
     */
    public function setType(?TeacherAbsenceType $type): TeacherAbsence {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string {
        return $this->message;
    }

    /**
     * @param string|null $message
     * @return TeacherAbsence
     */
    public function setMessage(?string $message): TeacherAbsence {
        $this->message = $message;
        return $this;
    }

    public function addComment(TeacherAbsenceComment $lesson): void {
        $lesson->setAbsence($this);
        $this->comments->add($lesson);
    }

    public function removeComment(TeacherAbsenceComment $lesson): void {
        $this->comments->removeElement($lesson);
    }

    /**
     * @return Collection<TeacherAbsenceComment>
     */
    public function getComments(): Collection {
        return $this->comments;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    /**
     * @return DateTime|null
     */
    public function getProcessedAt(): ?DateTime {
        return $this->processedAt;
    }

    /**
     * @param DateTime|null $processedAt
     * @return TeacherAbsence
     */
    public function setProcessedAt(?DateTime $processedAt): TeacherAbsence {
        $this->processedAt = $processedAt;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getProcessedBy(): ?User {
        return $this->processedBy;
    }

    /**
     * @param User|null $processedBy
     * @return TeacherAbsence
     */
    public function setProcessedBy(?User $processedBy): TeacherAbsence {
        $this->processedBy = $processedBy;
        return $this;
    }
}