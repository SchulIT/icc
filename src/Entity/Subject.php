<?php

namespace App\Entity;

use App\Repository\SubjectRepositoryInterface;
use App\Validator\Color;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @UniqueEntity(fields={"abbreviation"})
 */
class Subject {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @var string
     */
    private $abbreviation;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Exclude()
     * @var bool
     */
    private $replaceSubjectAbbreviation = false;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Exclude()
     * @var bool
     */
    private $isVisibleGrades = true;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Exclude()
     * @var bool
     */
    private $isVisibleStudents = true;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Exclude()
     * @var bool
     */
    private $isVisibleTeachers = true;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Exclude()
     * @var bool
     */
    private $isVisibleRooms = true;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Exclude()
     * @var bool
     */
    private $isVisibleSubjects = true;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Exclude()
     * @var bool
     */
    private $isVisibleCourses = true;

    /**
     * @ORM\ManyToOne(targetEntity="Teacher", inversedBy="departments")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Serializer\Exclude()
     * @var Teacher
     */
    private $departmentChairman;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(min="6", max="6")
     * @Color()
     * @Serializer\Exclude()
     */
    private $color;

    /**
     * @ORM\ManyToMany(targetEntity="Teacher", inversedBy="subjects")
     * @ORM\JoinTable(
     *     name="subject_teachers",
     *     joinColumns={@ORM\JoinColumn(name="subject", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="teacher", referencedColumnName="id")}
     * )
     * @var ArrayCollection<Teacher>
     */
    private $teachers;

    public function __construct() {
        $this->teachers = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAbbreviation(): string {
        return $this->abbreviation;
    }

    /**
     * @param string $abbreviation
     * @return Subject
     */
    public function setAbbreviation(string $abbreviation): Subject {
        $this->abbreviation = $abbreviation;
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
     * @return Subject
     */
    public function setName(string $name): Subject {
        $this->name = $name;
        return $this;
    }

    /**
     * @return bool
     */
    public function isReplaceSubjectAbbreviation(): bool {
        return $this->replaceSubjectAbbreviation;
    }

    /**
     * @param bool $replaceSubjectAbbreviation
     * @return Subject
     */
    public function setReplaceSubjectAbbreviation(bool $replaceSubjectAbbreviation): Subject {
        $this->replaceSubjectAbbreviation = $replaceSubjectAbbreviation;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVisibleGrades(): bool {
        return $this->isVisibleGrades;
    }

    /**
     * @param bool $isVisibleGrades
     * @return Subject
     */
    public function setIsVisibleGrades(bool $isVisibleGrades): Subject {
        $this->isVisibleGrades = $isVisibleGrades;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVisibleStudents(): bool {
        return $this->isVisibleStudents;
    }

    /**
     * @param bool $isVisibleStudents
     * @return Subject
     */
    public function setIsVisibleStudents(bool $isVisibleStudents): Subject {
        $this->isVisibleStudents = $isVisibleStudents;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVisibleTeachers(): bool {
        return $this->isVisibleTeachers;
    }

    /**
     * @param bool $isVisibleTeachers
     * @return Subject
     */
    public function setIsVisibleTeachers(bool $isVisibleTeachers): Subject {
        $this->isVisibleTeachers = $isVisibleTeachers;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVisibleRooms(): bool {
        return $this->isVisibleRooms;
    }

    /**
     * @param bool $isVisibleRooms
     * @return Subject
     */
    public function setIsVisibleRooms(bool $isVisibleRooms): Subject {
        $this->isVisibleRooms = $isVisibleRooms;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVisibleSubjects(): bool {
        return $this->isVisibleSubjects;
    }

    /**
     * @param bool $isVisibleSubjects
     * @return Subject
     */
    public function setIsVisibleSubjects(bool $isVisibleSubjects): Subject {
        $this->isVisibleSubjects = $isVisibleSubjects;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVisibleCourses(): bool {
        return $this->isVisibleCourses;
    }

    /**
     * @param bool $isVisibleCourses
     * @return Subject
     */
    public function setIsVisibleCourses(bool $isVisibleCourses): Subject {
        $this->isVisibleCourses = $isVisibleCourses;
        return $this;
    }

    /**
     * @return Teacher
     */
    public function getDepartmentChairman(): Teacher {
        return $this->departmentChairman;
    }

    /**
     * @param Teacher $departmentChairman
     * @return Subject
     */
    public function setDepartmentChairman(Teacher $departmentChairman): Subject {
        $this->departmentChairman = $departmentChairman;
        return $this;
    }

    /**
     * @return string
     */
    public function getColor(): string {
        return $this->color;
    }

    /**
     * @param string $color
     * @return Subject
     */
    public function setColor(string $color): Subject {
        $this->color = $color;
        return $this;
    }

    public function addTeacher(Teacher $teacher) {
        $this->teachers->add($teacher);
    }

    public function removeTeacher(Teacher $teacher) {
        $this->teachers->removeElement($teacher);
    }

    /**
     * @return ArrayCollection<Teacher>
     */
    public function getTeachers(): ArrayCollection {
        return $this->teachers;
    }

}