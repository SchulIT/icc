<?php

namespace App\Entity;

use App\Validator\NullOrNotBlank;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private $externalId;

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
     * @ORM\Column(type="gender")
     * @var Gender
     */
    private $gender;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Email()
     * @NullOrNotBlank()
     * @var string|null
     */
    private $email;

    /**
     * @ORM\ManyToMany(targetEntity="Subject", inversedBy="teachers")
     * @ORM\JoinTable(
     *     name="subject_teachers",
     *     joinColumns={@ORM\JoinColumn(name="teacher", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="subject", onDelete="CASCADE")}
     * )
     * @var Collection<Subject>
     */
    private $subjects;

    /**
     * @ORM\OneToMany(targetEntity="GradeTeacher", mappedBy="teacher")
     * @var Collection<GradeTeacher>
     */
    private $grades;

    /**
     * @ORM\ManyToMany(targetEntity="TeacherTag", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="teacher_tags",
     *     joinColumns={@ORM\JoinColumn(name="teacher", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="tag", onDelete="CASCADE")}
     * )
     */
    private $tags;

    public function __construct() {
        $this->subjects = new ArrayCollection();
        $this->grades = new ArrayCollection();
        $this->tags = new ArrayCollection();

        $this->setGender(Gender::X());
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
     * @param string|null $externalId
     * @return Teacher
     */
    public function setExternalId(?string $externalId): Teacher {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAcronym(): ?string {
        return $this->acronym;
    }

    /**
     * @param string|null $acronym
     * @return Teacher
     */
    public function setAcronym(?string $acronym): Teacher {
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
     * @return string|null
     */
    public function getFirstname(): ?string {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     * @return Teacher
     */
    public function setFirstname(?string $firstname): Teacher {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     * @return Teacher
     */
    public function setLastname(?string $lastname): Teacher {
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
     * @param string|null $email
     * @return Teacher
     */
    public function setEmail(?string $email): Teacher {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string {
        return $this->email;
    }

    /**
     * @return Collection<GradeTeacher>
     */
    public function getGrades(): Collection {
        return $this->grades;
    }

    public function addSubject(Subject $subject) {
        $this->subjects->add($subject);
    }

    public function removeSubject(Subject $subject) {
        $this->subjects->removeElement($subject);
    }

    /**
     * @return Collection<Subject>
     */
    public function getSubjects(): Collection {
        return $this->subjects;
    }

    public function addTag(TeacherTag $tag) {
        $this->tags->add($tag);
    }

    public function removeTag(TeacherTag $tag) {
        $this->tags->removeElement($tag);
    }

    /**
     * @return Collection<TeacherTag>
     */
    public function getTags(): Collection {
        return $this->tags;
    }

    public function __toString() {
        return $this->getAcronym();
    }
}