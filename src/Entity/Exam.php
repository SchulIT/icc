<?php

namespace App\Entity;

use App\Validator\DateInActiveSection;
use App\Validator\NoReservationCollision;
use App\Validator\NotTooManyExamsPerDay;
use App\Validator\NotTooManyExamsPerWeek;
use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[NotTooManyExamsPerDay]
#[NotTooManyExamsPerWeek]
#[NoReservationCollision]
#[ORM\Entity]
class Exam {

    use IdTrait;
    use UuidTrait;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string', unique: true, nullable: true)]
    private ?string $externalId = null;

    #[DateInActiveSection]
    #[Assert\NotNull]
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $date = null;

    #[Assert\GreaterThan(0)]
    #[ORM\Column(type: 'integer')]
    private int $lessonStart = 0;

    #[Assert\GreaterThan(0)]
    #[Assert\GreaterThanOrEqual(propertyPath: 'lessonStart')]
    #[ORM\Column(type: 'integer')]
    private int $lessonEnd = 0;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $description = null;

    #[ORM\Column(type: 'boolean')]
    private bool $tuitionTeachersCanEditExam = true;

    /**
     * @var Collection<Tuition>
     */
    #[ORM\JoinTable(name: 'exam_tuitions')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: Tuition::class)]
    private $tuitions;

    /**
     * @var Collection<Student>
     */
    #[ORM\JoinTable(name: 'exam_students')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: Student::class, cascade: ['persist'])]
    private $students;

    /**
     * @var Collection<ExamSupervision>
     */
    #[ORM\OneToMany(mappedBy: 'exam', targetEntity: ExamSupervision::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['lesson' => 'asc'])]
    private $supervisions;

    #[ORM\ManyToOne(targetEntity: Room::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Room $room = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();

        $this->tuitions = new ArrayCollection();
        $this->students = new ArrayCollection();
        $this->supervisions = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getExternalId(): ?string {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): Exam {
        $this->externalId = $externalId;
        return $this;
    }

    public function getDate(): ?DateTime {
        return $this->date;
    }

    public function setDate(?DateTime $date): Exam {
        $this->date = $date;
        return $this;
    }

    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    public function setLessonStart(int $lessonStart): Exam {
        $this->lessonStart = $lessonStart;
        return $this;
    }

    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }

    public function setLessonEnd(int $lessonEnd): Exam {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): Exam {
        $this->description = $description;
        return $this;
    }

    public function isTuitionTeachersCanEditExam(): bool {
        return $this->tuitionTeachersCanEditExam;
    }

    public function setTuitionTeachersCanEditExam(bool $tuitionTeachersCanEditExam): Exam {
        $this->tuitionTeachersCanEditExam = $tuitionTeachersCanEditExam;
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

    public function addSupervision(ExamSupervision $examSupervision) {
        $this->supervisions->add($examSupervision);
    }

    public function removeSupervision(ExamSupervision $examSupervision) {
        $this->supervisions->removeElement($examSupervision);
    }

    /**
     * @return Collection<ExamSupervision>
     */
    public function getSupervisions(): Collection {
        return $this->supervisions;
    }

    public function getRoom(): ?Room {
        return $this->room;
    }

    public function setRoom(?Room $room): Exam {
        $this->room = $room;
        return $this;
    }
}