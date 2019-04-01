<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     */
    private $grades;

    /**
     * @ORM\ManyToMany(targetEntity="Student")
     * @ORM\JoinTable(
     *     name="studygroup_students",
     *     joinColumns={@ORM\JoinColumn(name="studygroup", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="student", referencedColumnName="id")}
     * )
     */
    private $students;

    /**
     * @ORM\OneToMany(targetEntity="Tuition", mappedBy="studyGroup")
     * @var ArrayCollection<Tuition>
     */
    private $tuitions;

    public function __construct() {
        $this->grades = new ArrayCollection();
        $this->students = new ArrayCollection();
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
     * @return ArrayCollection<Grade>
     */
    public function getGrades(): ArrayCollection {
        return $this->grades;
    }

    public function addStudent(Student $student) {
        $this->students->add($student);
    }

    public function removeStudent(Student $student) {
        $this->students->removeElement($student);
    }

    /**
     * @return ArrayCollection<Student>
     */
    public function getStudents(): ArrayCollection {
        return $this->students;
    }

    /**
     * @return ArrayCollection<Tuition>
     */
    public function getTuitions(): ArrayCollection {
        return $this->tuitions;
    }

}