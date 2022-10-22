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

/**
 * @ORM\Entity()
 * @Auditable()
 */
class BookComment {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="text")
     */
    #[Assert\NotBlank]
    private ?string $text = null;

    /**
     * @ORM\Column(type="date")
     */
    #[Assert\NotNull]
    private ?\DateTime $date = null;

    /**
     * @ORM\ManyToOne(targetEntity="Teacher")
     * @ORM\JoinColumn()
     */
    #[Assert\NotNull]
    private ?Teacher $teacher = null;

    /**
     * @ORM\ManyToMany(targetEntity="Student")
     * @ORM\JoinTable("book_comment_student",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn()}
     * )
     * @var Collection<Student>
     */
    private $students;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private ?\DateTime $createdAt = null;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\Column(type="string")
     */
    private ?string $createdBy = null;

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
}