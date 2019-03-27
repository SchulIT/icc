<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @UniqueEntity(fields={"externalId"})
 */
class Substitution {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string|null
     */
    private $externalId;

    /**
     * @ORM\Column(type="date")
     * @Assert\Date()
     * @Assert\NotNull()
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
     * @Assert\GreaterThanOrEqual(propertyPath="lessonStart")
     * @var int
     */
    private $lessonEnd;

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
     * @ORM\ManyToOne(targetEntity="Teacher")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Teacher|null
     */
    private $teacher = null;

    /**
     * @ORM\ManyToOne(targetEntity="Teacher")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Teacher|null
     */
    private $replacementTeacher;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $room = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $replacementRoom = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $remark;

    /**
     * @ORM\ManyToMany(targetEntity="StudyGroup")
     * @ORM\JoinTable(
     *     name="substitution_studygroups",
     *     joinColumns={@ORM\JoinColumn(name="substitution", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="grade", referencedColumnName="id", onDelete="CASCADE")}
     * )
     * @var ArrayCollection<StudyGroup>
     */
    private $studyGroups;

    /**
     * @ORM\ManyToMany(targetEntity="StudyGroup")
     * @ORM\JoinTable(
     *     name="substitution_replacementstudygroups",
     *     joinColumns={@ORM\JoinColumn(name="substitution", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="grade", referencedColumnName="id", onDelete="CASCADE")}
     * )
     * @var ArrayCollection<StudyGroup>
     */
    private $replacementStudyGroups;

    /**
     * @return mixed
     */
    public function getId() {
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
     * @return Substitution
     */
    public function setExternalId(?string $externalId): Substitution {
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
     * @return Substitution
     */
    public function setDate(\DateTime $date): Substitution {
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
     * @return Teacher|null
     */
    public function getTeacher(): ?Teacher {
        return $this->teacher;
    }

    /**
     * @param Teacher|null $teacher
     * @return Substitution
     */
    public function setTeacher(?Teacher $teacher): Substitution {
        $this->teacher = $teacher;
        return $this;
    }

    /**
     * @return Teacher|null
     */
    public function getReplacementTeacher(): ?Teacher {
        return $this->replacementTeacher;
    }

    /**
     * @param Teacher|null $replacementTeacher
     * @return Substitution
     */
    public function setReplacementTeacher(?Teacher $replacementTeacher): Substitution {
        $this->replacementTeacher = $replacementTeacher;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRoom(): ?string {
        return $this->room;
    }

    /**
     * @param string|null $room
     * @return Substitution
     */
    public function setRoom(?string $room): Substitution {
        $this->room = $room;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getReplacementRoom(): ?string {
        return $this->replacementRoom;
    }

    /**
     * @param string|null $replacementRoom
     * @return Substitution
     */
    public function setReplacementRoom(?string $replacementRoom): Substitution {
        $this->replacementRoom = $replacementRoom;
        return $this;
    }

    /**
     * @return string
     */
    public function getRemark(): string {
        return $this->remark;
    }

    /**
     * @param string $remark
     * @return Substitution
     */
    public function setRemark(string $remark): Substitution {
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
     * @return ArrayCollection<StudyGroup>
     */
    public function getStudyGroups(): ArrayCollection {
        return $this->studyGroups;
    }

    public function addReplacementStudyGroup(StudyGroup $studyGroup) {
        $this->replacementStudyGroups->add($studyGroup);
    }

    public function removeReplacementStudyGroup(StudyGroup $studyGroup) {
        $this->replacementStudyGroups->removeElement($studyGroup);
    }

    /**
     * @return ArrayCollection<StudyGroup>
     */
    public function getReplacementStudyGroups(): ArrayCollection {
        return $this->replacementStudyGroups;
    }

}