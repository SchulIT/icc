<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class StudyGroup {

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
     * @Assert\NotBlank()
     * @var string
     */
    private $externalId;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="StudyGroupType::class")
     * @Assert\NotNull()
     * @var StudyGroupType
     */
    private $type;

    /**
     * @ORM\ManyToMany(targetEntity="Grade")
     * @ORM\JoinTable(
     *     name="studygroup_grades",
     *     joinColumns={@ORM\JoinColumn(name="studygroup", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="grade", referencedColumnName="id")}
     * )
     * @var Collection<Grade>
     */
    private $grades;

    /**
     * @ORM\OneToMany(targetEntity="StudyGroupMembership", mappedBy="studyGroup")
     * @var Collection<StudyGroupMembership>
     */
    private $memberships;

    /**
     * @ORM\OneToMany(targetEntity="Tuition", mappedBy="studyGroup")
     * @var Collection<Tuition>
     */
    private $tuitions;

    public function __construct() {
        $this->grades = new ArrayCollection();
        $this->memberships = new ArrayCollection();
        $this->tuitions = new ArrayCollection();
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
     * @return StudyGroup
     */
    public function setExternalId(string $externalId): StudyGroup {
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
     * @return StudyGroup
     */
    public function setName(string $name): StudyGroup {
        $this->name = $name;
        return $this;
    }

    /**
     * @return StudyGroupType
     */
    public function getType(): StudyGroupType {
        return $this->type;
    }

    /**
     * @param StudyGroupType $type
     * @return StudyGroup
     */
    public function setType(StudyGroupType $type): StudyGroup {
        $this->type = $type;
        return $this;
    }

    public function addGrade(Grade $grade) {
        $this->grades->add($grade);
    }

    public function removeGrade(Grade $grade) {
        $this->grades->removeElement($grade);
    }

    /**
     * @return Collection<Grade>
     */
    public function getGrades(): Collection {
        return $this->grades;
    }

    /**
     * @return Collection<StudyGroupMembership>
     */
    public function getMemberships(): Collection {
        return $this->memberships;
    }

    /**
     * @return Collection<Tuition>
     */
    public function getTuitions(): Collection {
        return $this->tuitions;
    }

}