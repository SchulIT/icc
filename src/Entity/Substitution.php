<?php

namespace App\Entity;

use DateTime;
use DH\DoctrineAuditBundle\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EasyCorp\Bundle\EasyAdminBundle\EventListener\RequestPostInitializeListener;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
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
     * @ORM\ManyToMany(targetEntity="Room")
     * @ORM\JoinTable(name="substitution_rooms",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @ORM\OrderBy({"name"="asc"})
     * @var Collection<Room>
     */
    private $rooms;

    /**
     * @ORM\ManyToMany(targetEntity="Room")
     * @ORM\JoinTable(name="substitution_replacement_rooms",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @ORM\OrderBy({"name"="asc"})
     * @var Collection<Room>
     */
    private $replacementRooms;

    /**
     * @ORM\Column(type="string", nullable=true, options={"comment": "Plain room name in case room resolve is not possible when importing substitutions."})
     * @var string|null
     */
    private $roomName = null;

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

    /**
     * @ORM\ManyToMany(targetEntity="Grade")
     * @ORM\JoinTable(name="substitution_replacement_grades",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var ArrayCollection<Grade>
     */
    private $replacementGrades;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $createdAt;

    public function __construct() {
        $this->uuid = Uuid::uuid4();

        $this->teachers = new ArrayCollection();
        $this->replacementTeachers = new ArrayCollection();
        $this->rooms = new ArrayCollection();
        $this->replacementRooms = new ArrayCollection();
        $this->studyGroups = new ArrayCollection();
        $this->replacementStudyGroups = new ArrayCollection();
        $this->replacementGrades = new ArrayCollection();
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

    public function addRoom(Room $room): void {
        $this->rooms->add($room);
    }

    public function removeRoom(Room $room): void {
        $this->rooms->removeElement($room);
    }

    public function getRooms(): Collection {
        return $this->rooms;
    }

    public function addReplacementRoom(Room $room): void {
        $this->replacementRooms->add($room);
    }

    public function removeReplacementRoom(Room $room): void {
        $this->replacementRooms->removeElement($room);
    }

    public function getReplacementRooms(): Collection {
        return $this->replacementRooms;
    }


    /**
     * @return string|null
     */
    public function getRoomsAsString(): ?string {
        if($this->getRooms()->count() > 0) {
            return implode(', ', $this->getRooms()->map(function(Room $room) { return $room->getName(); })->toArray());
        }

        return $this->getRoomName();
    }

    /**
     * @return string|null
     */
    public function getReplacementRoomsAsString(): ?string {
        if($this->getReplacementRooms()->count() > 0) {
            return implode(', ', $this->getReplacementRooms()->map(function(Room $room) { return $room->getName(); })->toArray());
        }

        return $this->getReplacementRoomName();
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

    public function addReplacementGrade(Grade $grade): void {
        $this->replacementGrades->add($grade);
    }

    public function removeReplacementGrade(Grade $grade): void {
        $this->replacementGrades->removeElement($grade);
    }

    public function getReplacementGrades(): Collection {
        return $this->replacementGrades;
    }

    /**
     * @return Grade[]
     */
    public function getGrades(): array {
        $grades = [ ];

        if($this->getReplacementGrades()->count() > 0) {
            return $this->getReplacementGrades()->toArray();
        }

        /** @var StudyGroup $studyGroup */
        foreach($this->getStudyGroups() as $studyGroup) {
            /** @var Grade $grade */
            foreach($studyGroup->getGrades() as $grade) {
                if(!in_array($grade, $grades)) {
                    $grades[] = $grade;
                }
            }
        }

        /** @var StudyGroup $studyGroup */
        foreach($this->getReplacementStudyGroups() as $studyGroup) {
            /** @var Grade $grade */
            foreach($studyGroup->getGrades() as $grade) {
                if(!in_array($grade, $grades)) {
                    $grades[] = $grade;
                }
            }
        }

        return $grades;
    }

    public function clone() {
        $clone = new self();

        $clone->setDate($this->getDate());
        $clone->setType($this->getType());
        $clone->setExternalId($this->getExternalId());
        $clone->setSubject($this->getSubject());
        $clone->setReplacementSubject($this->getReplacementSubject());
        $clone->setRoomName($this->getRoomName());
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

        foreach($this->getRooms() as $room) {
            $clone->addRoom($room);
        }

        foreach($this->getReplacementRooms() as $room) {
            $clone->addReplacementRoom($room);
        }

        foreach($this->getReplacementGrades() as $grade) {
            $clone->addReplacementGrade($grade);
        }

        return $clone;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }
}