<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @UniqueEntity(fields={"acronym"})
 */
class Teacher {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string
     */
    private $acronym;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $title;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @var string
     */
    private $firstname;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @var string
     */
    private $lastname;

    /**
     * @ORM\Column(type="Gender::class")
     * @var Gender
     */
    private $gender;

    /**
     * @ORM\ManyToMany(targetEntity="Grade")
     * @ORM\JoinTable(name="teacher_grades",
     *     joinColumns={@ORM\JoinColumn(name="teacher", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="grade", referencedColumnName="id", onDelete="CASCADE")}
     * )
     * @var ArrayCollection<Grade>
     */
    private $grades;

    /**
     * @ORM\ManyToMany(targetEntity="Grade")
     * @ORM\JoinTable(name="teacher_grade_substitutes",
     *     joinColumns={@ORM\JoinColumn(name="teacher", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="grade", referencedColumnName="id", onDelete="CASCADE")}
     * )
     * @var ArrayCollection<Grade>
     */
    private $gradesSubstitutes;

    /**
     * @ORM\ManyToMany(targetEntity="Subject", mappedBy="teachers")
     * @var ArrayCollection<Subject>
     */
    private $subjects;

    public function __construct() {
        $this->grades = new ArrayCollection();
        $this->gradesSubstitutes = new ArrayCollection();
        $this->subjects = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAcronym(): string {
        return $this->acronym;
    }

    /**
     * @param string $acronym
     * @return Teacher
     */
    public function setAcronym(string $acronym): Teacher {
        $this->acronym = $acronym;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string {
        return $this->title;
    }

    /**
     * @param string|null $title
     * @return Teacher
     */
    public function setTitle(?string $title): Teacher {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstname(): string {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     * @return Teacher
     */
    public function setFirstname(string $firstname): Teacher {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastname(): string {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     * @return Teacher
     */
    public function setLastname(string $lastname): Teacher {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return Gender
     */
    public function getGender(): Gender {
        return $this->gender;
    }

    /**
     * @param Gender $gender
     * @return Teacher
     */
    public function setGender(Gender $gender): Teacher {
        $this->gender = $gender;
        return $this;
    }

    /**
     * @param Grade $grade
     */
    public function addGrade(Grade $grade): void {
        $this->grades->add($grade);
    }

    /**
     * @param Grade $grade
     */
    public function removeGrade(Grade $grade): void {
        $this->grades->removeElement($grade);
    }

    /**
     * @return ArrayCollection<Grade>
     */
    public function getGrades(): ArrayCollection {
        return $this->grades;
    }

    /**
     * @param Grade $grade
     */
    public function addGradeSubstitute(Grade $grade): void {
        $this->gradesSubstitutes->add($grade);
    }

    /**
     * @param Grade $grade
     */
    public function removeGradeSubstitute(Grade $grade): void {
        $this->gradesSubstitutes->removeElement($grade);
    }

    /**
     * @return ArrayCollection<Grade>
     */
    public function getGradeSubstitutes(): ArrayCollection {
        return $this->gradesSubstitutes;
    }

    /**
     * @return ArrayCollection<Subject>
     */
    public function getSubjects(): ArrayCollection {
        return $this->subjects;
    }
}