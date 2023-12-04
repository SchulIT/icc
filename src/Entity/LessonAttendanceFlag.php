<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[ORM\Entity]
class LessonAttendanceFlag implements JsonSerializable {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string')]
    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $icon;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\Length(max: 255)]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $stackIcon;

    #[ORM\Column(type: 'string')]
    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $description;

    /**
     * @var Collection<Subject>
     */
    #[ORM\ManyToMany(targetEntity: Subject::class)]
    #[ORM\JoinTable]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    private Collection $subjects;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->subjects = new ArrayCollection();
    }

    public function getIcon(): ?string {
        return $this->icon;
    }

    public function setIcon(?string $icon): LessonAttendanceFlag {
        $this->icon = $icon;
        return $this;
    }

    public function getStackIcon(): ?string {
        return $this->stackIcon;
    }

    public function setStackIcon(?string $stackIcon): LessonAttendanceFlag {
        $this->stackIcon = $stackIcon;
        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): LessonAttendanceFlag {
        $this->description = $description;
        return $this;
    }

    public function addSubject(Subject $subject): void {
        $this->subjects->add($subject);
    }

    public function removeSubject(Subject $subject): void {
        $this->subjects->removeElement($subject);
    }

    /**
     * @return Collection<Subject>
     */
    public function getSubjects(): Collection {
        return $this->subjects;
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid->toString(),
            'icon' => $this->icon,
            'stack_icon' => $this->stackIcon,
            'description' => $this->description
        ];
    }
}