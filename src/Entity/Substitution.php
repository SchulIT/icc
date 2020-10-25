<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @UniqueEntity(fields={"externalId"})
 */
class Substitution {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string|null
     */
    private $externalId;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotNull()
     * @var DateTime
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(0)
     * @var int
     */
    private $lessonStart = 0;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(0)
     * @Assert\GreaterThanOrEqual(propertyPath="lessonStart")
     * @var int
     */
    private $lessonEnd = 0;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $startsBefore = false;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $type = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $subject = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $replacementSubject = null;

    /**
     * @ORM\ManyToMany(targetEntity="Teacher")
     * @ORM\JoinTable(name="substitution_teachers",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var Collection<Teacher>
     */
    private $teachers;

    /**
     * @ORM\ManyToMany(targetEntity="Teacher")
     * @ORM\JoinTable(name="substitution_replacement_teachers",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var Collection<Teacher>
     */
    private $replacementTeachers;

    /**
     * @ORM\ManyToOne(targetEntity="Room")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @var Room|null
     */
    private $room = null;

    /**
     * @ORM\Column(type="string", nullable=true, options={"comment": "Plain room name in case room resolve is not possible when importing substitutions."})
     * @var string|null
     */
    private $roomName = null;

    /**
     * @ORM\ManyToOne(targetEntity="Room")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @var Room|null
     */
    private $replacementRoom = null;

    /**
     * @ORM\Column(type="string", nullable=true, options={"comment": "Plain room name in case room resolve is not possible when importing substitutions."})
     * @var string|null
     */
    private $replacementRoomName = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $remark;

    /**
     * @ORM\ManyToMany(targetEntity="StudyGroup")
     * @ORM\JoinTable(name="substitution_studygroups",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var ArrayCollection<StudyGroup>
     */
    private $studyGroups;

    /**
     * @ORM\ManyToMany(targetEntity="StudyGroup")
     * @ORM\JoinTable(name="substitution_replacement_studygroups",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var ArrayCollection<StudyGroup>
     */
    private $replacementStudyGroups;

    public function __construct() {
        $this->uuid = Uuid::uuid4();

        $this->teachers = new ArrayCollection();
        $this->replacementTeachers = new ArrayCollection();
        $this->studyGroups = new ArrayCollection();
        $this->replacementStudyGroups = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string {
        return $this->externalId;
    }

    /**
     * @param string|null $externalId
     * @return Substitution
     */
    public function setExternalId(?string $externalId): Substitution {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getDate(): ?DateTime  {
        return $this->date;
    }

    /**
     * @param DateTime|null $date
     * @return Substitution
     */
    public function setDate(?DateTime $date): Substitution {
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
     * @return Substitution
     */
    public function setLessonStart(int $lessonStart): Substitution {
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
     * @return Substitution
     */
    public function setLessonEnd(int $lessonEnd): Substitution {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    /**
     * @return bool
     */
    public function startsBefore(): bool {
        return $this->startsBefore;
    }

    /**
     * @param bool $startsBefore
     * @return Substitution
     */
    public function setStartsBefore(bool $startsBefore): Substitution {
        $this->startsBefore = $startsBefore;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string {
        return $this->type;
    }

    /**
     * @param string|null $type
     * @return Substitution
     */
    public function setType(?string $type): Substitution {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSubject(): ?string {
        return $this->subject;
    }

    /**
     * @param string|null $subject
     * @return Substitution
     */
    public function setSubject(?string $subject): Substitution {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getReplacementSubject(): ?string {
        return $this->replacementSubject;
    }

    /**
     * @param string|null $replacementSubject
     * @return Substitution
     */
    public function setReplacementSubject(?string $replacementSubject): Substitution {
        $this->replacementSubject = $replacementSubject;
        return $this;
    }

    /**
     * @return Collection<Teacher>s
     */
    public function getTeachers(): Collection {
        return $this->teachers;
    }

    public function addTeacher(Teacher $teacher): void {
        $this->teachers->add($teacher);
    }

    public function removeTeacher(Teacher $teacher): void {
        $this->teachers->removeElement($teacher);
    }

    /**
     * @return Collection<Teacher>
     */
    public function getReplacementTeachers(): Collection {
        return $this->replacementTeachers;
    }

    public function addReplacementTeacher(Teacher $teacher): void {
        $this->replacementTeachers->add($teacher);
    }

    public function removeReplacementTeacher(Teacher $teacher): void {
        $this->replacementTeachers->removeElement($teacher);
    }

    /**
     * @return Room|null
     */
    public function getRoom(): ?Room {
        return $this->room;
    }

    /**
     * @param Room|null $room
     * @return Substitution
     */
    public function setRoom(?Room $room): Substitution {
        $this->room = $room;
        return $this;
    }

    /**
     * @return Room|null
     */
    public function getReplacementRoom(): ?Room {
        return $this->replacementRoom;
    }

    /**
     * @param Room|null $replacementRoom
     * @return Substitution
     */
    public function setReplacementRoom(?Room $replacementRoom): Substitution {
        $this->replacementRoom = $replacementRoom;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRoomName(): ?string {
        return $this->roomName;
    }

    /**
     * @param string|null $roomName
     * @return Substitution
     */
    public function setRoomName(?string $roomName): Substitution {
        $this->roomName = $roomName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getReplacementRoomName(): ?string {
        return $this->replacementRoomName;
    }

    /**
     * @param string|null $replacementRoomName
     * @return Substitution
     */
    public function setReplacementRoomName(?string $replacementRoomName): Substitution {
        $this->replacementRoomName = $replacementRoomName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRemark(): ?string {
        return $this->remark;
    }

    /**
     * @param string|null $remark
     * @return Substitution
     */
    public function setRemark(?string $remark): Substitution {
        $this->remark = $remark;
        return $this;
    }

    public function addStudyGroup(StudyGroup $studyGroup) {
        $this->studyGroups->add($studyGroup);
    }

    public function removeStudyGroup(StudyGroup $studyGroup) {
        $this->studyGroups->removeElement($studyGroup);
    }

    /**
     * @return Collection<StudyGroup>
     */
    public function getStudyGroups(): Collection {
        return $this->studyGroups;
    }

    public function addReplacementStudyGroup(StudyGroup $studyGroup) {
        $this->replacementStudyGroups->add($studyGroup);
    }

    public function removeReplacementStudyGroup(StudyGroup $studyGroup) {
        $this->replacementStudyGroups->removeElement($studyGroup);
    }

    /**
     * @return Collection<StudyGroup>
     */
    public function getReplacementStudyGroups(): Collection {
        return $this->replacementStudyGroups;
    }

    public function clone() {
        $clone = new self();

        $clone->setDate($this->getDate());
        $clone->setType($this->getType());
        $clone->setExternalId($this->getExternalId());
        $clone->setSubject($this->getSubject());
        $clone->setReplacementSubject($this->getSubject());
        $clone->setRoom($this->getRoom());
        $clone->setRoomName($this->getRoomName());
        $clone->setReplacementRoom($this->getReplacementRoom());
        $clone->setReplacementRoomName($this->getReplacementRoomName());
        $clone->setLessonStart($this->getLessonStart());
        $clone->setLessonEnd($this->getLessonEnd());
        $clone->setStartsBefore($this->startsBefore());
        $clone->setRemark($this->getRemark());

        foreach($this->getTeachers() as $teacher) {
            $clone->addTeacher($teacher);
        }

        foreach($this->getReplacementTeachers() as $teacher) {
            $clone->addReplacementTeacher($teacher);
        }

        foreach($this->getStudyGroups() as $studyGroup) {
            $clone->addStudyGroup($studyGroup);
        }

        foreach($this->getReplacementStudyGroups() as $studyGroup) {
            $clone->addReplacementStudyGroup($studyGroup);
        }

        return $clone;
    }
}