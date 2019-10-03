<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class Exam {

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
     * @ORM\Column(type="date")
     * @Assert\NotNull()
     * @Assert\Date()
     * @var \DateTime
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(0)
     * @var int
     */
    private $lessonStart;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(0)
     * @Assert\GreaterThan(propertyPath="lessonStart")
     * @var int
     */
    private $lessonEnd;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="Tuition")
     * @ORM\JoinTable(
     *     name="exam_tuitions",
     *     joinColumns={@ORM\JoinColumn(name="exam", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="tuition", onDelete="CASCADE")}
     * )
     * @var Collection<Tuition>
     */
    private $tuitions;

    /**
     * @ORM\ManyToMany(targetEntity="Student")
     * @ORM\JoinTable(
     *     name="exam_students",
     *     joinColumns={@ORM\JoinColumn(name="exam", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="student", onDelete="CASCADE")}
     * )
     * @var Collection<Student>
     */
    private $students;

    /**
     * @ORM\OneToMany(targetEntity="ExamInvigilator", mappedBy="exam")
     * @ORM\OrderBy({"lesson" = "asc"})
     * @var Collection<ExamInvigilator>
     */
    private $invigilators;

    /**
     * @ORM\Column(type="json_array")
     * @var string[]
     */
    private $rooms = [ ];

    public function __construct() {
        $this->tuitions = new ArrayCollection();
        $this->students = new ArrayCollection();
        $this->invigilators = new ArrayCollection();
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
     * @return Exam
     */
    public function setExternalId(?string $externalId): Exam {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return Exam
     */
    public function setDate(\DateTime $date): Exam {
        $this->date = $date;
        return $this;
    }

    /**
     * @return int
     */
    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    /**
     * @param int $lessonStart
     * @return Exam
     */
    public function setLessonStart(int $lessonStart): Exam {
        $this->lessonStart = $lessonStart;
        return $this;
    }

    /**
     * @return int
     */
    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }

    /**
     * @param int $lessonEnd
     * @return Exam
     */
    public function setLessonEnd(int $lessonEnd): Exam {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return Exam
     */
    public function setDescription(?string $description): Exam {
        $this->description = $description;
        return $this;
    }

    public function addTuition(Tuition $tuition) {
        $this->tuitions->add($tuition);
    }

    public function removeTuition(Tuition $tuition) {
        $this->tuitions->removeElement($tuition);
    }

    /**
     * @return Collection<Tuition>
     */
    public function getTuitions(): Collection {
        return $this->tuitions;
    }

    /**
     * @return Collection<Student>
     */
    public function getStudents(): Collection {
        return $this->students;
    }

    public function addStudent(Student $student) {
        $this->students->add($student);
    }

    public function removeStudent(Student $student) {
        $this->students->removeElement($student);
    }

    public function addInvigilator(ExamInvigilator $examInvigilator) {
        $this->invigilators->add($examInvigilator);
    }

    public function removeInvigilator(ExamInvigilator $examInvigilator) {
        $this->invigilators->removeElement($examInvigilator);
    }

    /**
     * @return Collection<ExamInvigilator>
     */
    public function getInvigilators(): Collection {
        return $this->invigilators;
    }

    /**
     * @return string[]
     */
    public function getRooms(): array {
        return $this->rooms;
    }

    /**
     * @param string[] $rooms
     * @return Exam
     */
    public function setRooms(array $rooms): Exam {
        $this->rooms = $rooms;
        return $this;
    }
}