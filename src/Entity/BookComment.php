<?php

namespace App\Entity;

use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[ORM\Entity]
class BookComment {

    use IdTrait;
    use UuidTrait;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'text')]
    private ?string $text = null;

    #[Assert\NotNull]
    #[ORM\Column(type: 'date')]
    private ?DateTime $date = null;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Teacher::class)]
    #[ORM\JoinColumn]
    private ?Teacher $teacher = null;

    /**
     * @var Collection<Student>
     */
    #[ORM\JoinTable('book_comment_student')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn]
    #[ORM\ManyToMany(targetEntity: Student::class)]
    private $students;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    private ?DateTime $createdAt = null;

    #[Gedmo\Blameable(on: 'create')]
    #[ORM\Column(type: 'string')]
    private ?string $createdBy = null;

    #[ORM\Column(type: 'boolean')]
    private bool $canStudentAndParentsView = false;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->students = new ArrayCollection();
    }

    public function getText(): ?string {
        return $this->text;
    }

    public function setText(?string $text): BookComment {
        $this->text = $text;
        return $this;
    }

    public function getDate(): ?DateTime {
        return $this->date;
    }

    public function setDate(?DateTime $date): BookComment {
        $this->date = $date;
        return $this;
    }

    public function getTeacher(): ?Teacher {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): BookComment {
        $this->teacher = $teacher;
        return $this;
    }

    public function addStudent(Student $student): void {
        $this->students->add($student);
    }

    public function removeStudent(Student $student): void {
        $this->students->removeElement($student);
    }

    /**
     * @return Collection
     */
    public function getStudents() {
        return $this->students;
    }

    public function getCreatedAt(): ?DateTime {
        return $this->createdAt;
    }

    public function getCreatedBy(): ?string {
        return $this->createdBy;
    }

    public function canStudentAndParentsView(): bool {
        return $this->canStudentAndParentsView;
    }

    public function setCanStudentAndParentsView(bool $notifyStudentAndParents): void {
        $this->canStudentAndParentsView = $notifyStudentAndParents;
    }
}