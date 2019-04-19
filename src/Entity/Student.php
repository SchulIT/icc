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
 * @UniqueEntity(fields={"internalId"})
 */
class Student {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotNull()
     * @var string
     */
    private $externalId;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @var string
     */
    private $firstname;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @var string
     */
    private $lastname;

    /**
     * @ORM\Column(type="Gender::class")
     * @var Gender
     */
    private $gender;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @NullOrNotBlank()
     * @Assert\Email()
     * @var string|null
     */
    private $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $status;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isFullAged = false;

    /**
     * @ORM\ManyToOne(targetEntity="Grade", inversedBy="students")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @var Grade
     */
    private $grade;

    /**
     * @ORM\OneToMany(targetEntity="StudyGroupMembership", mappedBy="student")
     * @var Collection<StudyGroupMembership>
     */
    private $studyGroupMemberships;

    public function __construct() {
        $this->studyGroupMemberships = new ArrayCollection();
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
    public function getExternalId(): string {
        return $this->externalId;
    }

    /**
     * @param string $externalId
     * @return Student
     */
    public function setExternalId(string $externalId): Student {
        $this->externalId = $externalId;
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
     * @return Student
     */
    public function setFirstname(string $firstname): Student {
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
     * @return Student
     */
    public function setLastname(string $lastname): Student {
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
     * @return Student
     */
    public function setGender(Gender $gender): Student {
        $this->gender = $gender;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return Student
     */
    public function setEmail(?string $email): Student {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string {
        return $this->status;
    }

    /**
     * @param string|null $status
     * @return Student
     */
    public function setStatus(?string $status): Student {
        $this->status = $status;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFullAged(): bool {
        return $this->isFullAged;
    }

    /**
     * @param bool $isFullAged
     * @return Student
     */
    public function setIsFullAged(bool $isFullAged): Student {
        $this->isFullAged = $isFullAged;
        return $this;
    }

    /**
     * @return Grade|null
     */
    public function getGrade(): ?Grade {
        return $this->grade;
    }

    /**
     * @param Grade|null $grade
     * @return Student
     */
    public function setGrade(?Grade $grade): Student {
        $this->grade = $grade;
        return $this;
    }

    /**
     * @return Collection<StudyGroupMembership>
     */
    public function getStudyGroupMemberships(): Collection {
        return $this->studyGroupMemberships;
    }
}