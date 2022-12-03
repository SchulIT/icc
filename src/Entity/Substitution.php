<?php

namespace App\Entity;

use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[UniqueEntity(fields: ['externalId'])]
#[ORM\Entity]
class Substitution {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string', unique: true)]
    private ?string $externalId = null;

    #[Assert\NotNull]
    #[ORM\Column(type: 'date')]
    private ?DateTime $date = null;

    #[Assert\GreaterThan(0)]
    #[ORM\Column(type: 'integer')]
    private int $lessonStart = 0;

    #[Assert\GreaterThan(0)]
    #[Assert\GreaterThanOrEqual(propertyPath: 'lessonStart')]
    #[ORM\Column(type: 'integer')]
    private int $lessonEnd = 0;

    #[ORM\Column(type: 'boolean')]
    private bool $startsBefore = false;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $type = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $subject = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $replacementSubject = null;

    /**
     * @var Collection<Teacher>
     */
    #[ORM\JoinTable(name: 'substitution_teachers')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: Teacher::class)]
    private $teachers;

    /**
     * @var Collection<Teacher>
     */
    #[ORM\JoinTable(name: 'substitution_replacement_teachers')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: Teacher::class)]
    private $replacementTeachers;

    /**
     * @var Collection<Room>
     */
    #[ORM\JoinTable(name: 'substitution_rooms')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: Room::class)]
    #[ORM\OrderBy(['name' => 'asc'])]
    private $rooms;

    /**
     * @var Collection<Room>
     */
    #[ORM\JoinTable(name: 'substitution_replacement_rooms')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: Room::class)]
    #[ORM\OrderBy(['name' => 'asc'])]
    private $replacementRooms;

    #[ORM\Column(type: 'string', nullable: true, options: ['comment' => 'Plain room name in case room resolve is not possible when importing substitutions.'])]
    private ?string $roomName = null;

    #[ORM\Column(type: 'string', nullable: true, options: ['comment' => 'Plain room name in case room resolve is not possible when importing substitutions.'])]
    private ?string $replacementRoomName = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $remark = null;

    /**
     * @var ArrayCollection<StudyGroup>
     */
    #[ORM\JoinTable(name: 'substitution_studygroups')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: StudyGroup::class)]
    private $studyGroups;

    /**
     * @var ArrayCollection<StudyGroup>
     */
    #[ORM\JoinTable(name: 'substitution_replacement_studygroups')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: StudyGroup::class)]
    private $replacementStudyGroups;

    /**
     * @var ArrayCollection<Grade>
     */
    #[ORM\JoinTable(name: 'substitution_replacement_grades')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: Grade::class)]
    private $replacementGrades;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

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

    public function getExternalId(): ?string {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): Substitution {
        $this->externalId = $externalId;
        return $this;
    }

    public function getDate(): ?DateTime  {
        return $this->date;
    }

    public function setDate(?DateTime $date): Substitution {
        $this->date = $date;
        return $this;
    }

    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    public function setLessonStart(int $lessonStart): Substitution {
        $this->lessonStart = $lessonStart;
        return $this;
    }

    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }

    public function setLessonEnd(int $lessonEnd): Substitution {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    public function startsBefore(): bool {
        return $this->startsBefore;
    }

    public function setStartsBefore(bool $startsBefore): Substitution {
        $this->startsBefore = $startsBefore;
        return $this;
    }

    public function getType(): ?string {
        return $this->type;
    }

    public function setType(?string $type): Substitution {
        $this->type = $type;
        return $this;
    }

    public function getSubject(): ?string {
        return $this->subject;
    }

    public function setSubject(?string $subject): Substitution {
        $this->subject = $subject;
        return $this;
    }

    public function getReplacementSubject(): ?string {
        return $this->replacementSubject;
    }

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


    public function getRoomsAsString(): ?string {
        if($this->getRooms()->count() > 0) {
            return implode(', ', $this->getRooms()->map(fn(Room $room) => $room->getName())->toArray());
        }

        return $this->getRoomName();
    }

    public function getReplacementRoomsAsString(): ?string {
        if($this->getReplacementRooms()->count() > 0) {
            return implode(', ', $this->getReplacementRooms()->map(fn(Room $room) => $room->getName())->toArray());
        }

        return $this->getReplacementRoomName();
    }

    public function getRoomName(): ?string {
        return $this->roomName;
    }

    public function setRoomName(?string $roomName): Substitution {
        $this->roomName = $roomName;
        return $this;
    }

    public function getReplacementRoomName(): ?string {
        return $this->replacementRoomName;
    }

    public function setReplacementRoomName(?string $replacementRoomName): Substitution {
        $this->replacementRoomName = $replacementRoomName;
        return $this;
    }

    public function getRemark(): ?string {
        return $this->remark;
    }

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

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }
}