<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Checklist {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $title;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTime $dueDate = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $canStudentsView = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $canParentsView = false;

    #[Gedmo\Blameable]
    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $createdBy;

    /**
     * @var Collection<ChecklistStudent>
     */
    #[OneToMany(mappedBy: 'checklist', targetEntity: ChecklistStudent::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $students;

    /**
     * @var Collection<User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    private Collection $sharedWith;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->students = new ArrayCollection();
        $this->sharedWith = new ArrayCollection();
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $title): Checklist {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): Checklist {
        $this->description = $description;
        return $this;
    }

    public function getDueDate(): ?DateTime {
        return $this->dueDate;
    }

    public function setDueDate(?DateTime $dueDate): Checklist {
        $this->dueDate = $dueDate;
        return $this;
    }

    public function isCanStudentsView(): bool {
        return $this->canStudentsView;
    }

    public function setCanStudentsView(bool $canStudentsView): Checklist {
        $this->canStudentsView = $canStudentsView;
        return $this;
    }

    public function isCanParentsView(): bool {
        return $this->canParentsView;
    }

    public function setCanParentsView(bool $canParentsView): Checklist {
        $this->canParentsView = $canParentsView;
        return $this;
    }

    public function getCreatedBy(): User {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): Checklist {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function addStudent(ChecklistStudent $student): void {
        $this->students->add($student);
    }

    public function removeStudent(ChecklistStudent $student): void {
        $this->students->removeElement($student);
    }

    public function getStudents(): Collection {
        return $this->students;
    }

    public function addSharedWith(User $user): void {
        $this->sharedWith->add($user);
    }

    public function removeSharedWith(User $user): void {
        $this->sharedWith->removeElement($user);
    }

    public function getSharedWith(): Collection {
        return $this->sharedWith;
    }
}