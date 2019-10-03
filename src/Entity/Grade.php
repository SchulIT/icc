<?php

namespace App\Entity;

use App\Utils\CollectionUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class Grade {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     * @var string|null
     */
    private $externalId;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @var string
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Student", mappedBy="grade")
     * @var ArrayCollection<Student>
     */
    private $students;

    /**
     * @ORM\OneToMany(targetEntity="GradeTeacher", mappedBy="grade", cascade={"persist"})
     * @var ArrayCollection<GradeTeacher>
     */
    private $teachers;

    public function __construct() {
        $this->students = new ArrayCollection();
        $this->teachers = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string {
        return $this->externalId;
    }

    /**
     * @param string $externalId
     * @return Grade
     */
    public function setExternalId(string $externalId): Grade {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Grade
     */
    public function setName(string $name): Grade {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection<Student>
     */
    public function getStudents(): Collection {
        return $this->students;
    }

    public function addTeacher(GradeTeacher $teacher) {
        $this->teachers->add($teacher);
    }

    public function removeTeacher(GradeTeacher $teacher) {
        $this->teachers->removeElement($teacher);
    }

    public function getTeachers(): Collection {
        return $this->teachers;
    }

}