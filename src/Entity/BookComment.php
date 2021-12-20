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
     * @Assert\NotBlank()
     * @var string|null
     */
    private $text;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotNull()
     * @var DateTime|null
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="Teacher")
     * @ORM\JoinColumn()
     * @Assert\NotNull()
     * @var Teacher|null
     */
    private $teacher;

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
     * @var DateTime|null
     */
    private $createdAt;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\Column(type="string")
     * @var string|null
     */
    private $createdBy;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->students = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getText(): ?string {
        return $this->text;
    }

    /**
     * @param string|null $text
     * @return BookComment
     */
    public function setText(?string $text): BookComment {
        $this->text = $text;
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
     * @return BookComment
     */
    public function setDate(?DateTime $date): BookComment {
        $this->date = $date;
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
     * @return BookComment
     */
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

    /**
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime {
        return $this->createdAt;
    }

    /**
     * @return string|null
     */
    public function getCreatedBy(): ?string {
        return $this->createdBy;
    }
}