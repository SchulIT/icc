<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class Appointment {

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
    private $externalId = null;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @var string
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string|null
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     * @Assert\NotNull()
     * @var \DateTime
     */
    private $start;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\GreaterThan(propertyPath="start")
     * @var \DateTime
     */
    private $end;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $location;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $allDay = true;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isHiddenFromStudents;

    /**
     * @ORM\ManyToMany(targetEntity="StudyGroup", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="appointment_studygroups",
     *     joinColumns={@ORM\JoinColumn(name="grade", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="studygroup", onDelete="CASCADE")}
     * )
     * @var ArrayCollection<StudyGroup>
     */
    private $studyGroups;

    /**
     * @ORM\ManyToMany(targetEntity="Teacher", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="appointment_organizers",
     *     joinColumns={@ORM\JoinColumn(name="appointment", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="teacher", onDelete="CASCADE")}
     * )
     * @var ArrayCollection<Teacher>
     */
    private $organizers;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $externalOrganizers;

    /**
     * @ORM\ManyToOne(targetEntity="AppointmentCategory")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var AppointmentCategory
     */
    private $category;

    public function __construct() {
        $this->studyGroups = new ArrayCollection();
        $this->organizers = new ArrayCollection();
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
     * @return Appointment
     */
    public function setExternalId(?string $externalId): Appointment {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Appointment
     */
    public function setTitle(string $title): Appointment {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string {
        return $this->content;
    }

    /**
     * @param string|null $content
     * @return Appointment
     */
    public function setContent(?string $content): Appointment {
        $this->content = $content;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStart(): \DateTime {
        return $this->start;
    }

    /**
     * @param \DateTime $start
     * @return Appointment
     */
    public function setStart(\DateTime $start): Appointment {
        $this->start = $start;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEnd(): \DateTime {
        return $this->end;
    }

    /**
     * @param \DateTime $end
     * @return Appointment
     */
    public function setEnd(\DateTime $end): Appointment {
        $this->end = $end;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocation(): ?string {
        return $this->location;
    }

    /**
     * @param string|null $location
     * @return Appointment
     */
    public function setLocation(?string $location): Appointment {
        $this->location = $location;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAllDay(): bool {
        return $this->allDay;
    }

    /**
     * @param bool $allDay
     * @return Appointment
     */
    public function setAllDay(bool $allDay): Appointment {
        $this->allDay = $allDay;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHiddenFromStudents(): bool {
        return $this->isHiddenFromStudents;
    }

    /**
     * @param bool $isHiddenFromStudents
     * @return Appointment
     */
    public function setIsHiddenFromStudents(bool $isHiddenFromStudents): Appointment {
        $this->isHiddenFromStudents = $isHiddenFromStudents;
        return $this;
    }

    /**
     * @return AppointmentCategory|null
     */
    public function getCategory(): ?AppointmentCategory {
        return $this->category;
    }

    /**
     * @param AppointmentCategory $category
     * @return Appointment
     */
    public function setCategory(AppointmentCategory $category): Appointment {
        $this->category = $category;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getExternalOrganizers(): ?string {
        return $this->externalOrganizers;
    }

    /**
     * @param string|null $externalOrganizers
     * @return Appointment
     */
    public function setExternalOrganizers(?string $externalOrganizers): Appointment {
        $this->externalOrganizers = $externalOrganizers;
        return $this;
    }

    public function addOrganizer(Teacher $teacher) {
        $this->organizers->add($teacher);
    }

    public function removeOrganizer(Teacher $teacher) {
        $this->organizers->removeElement($teacher);
    }

    /**
     * @return ArrayCollection<Teacher>
     */
    public function getOrganizers() {
        return $this->organizers;
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
    public function getStudyGroups() {
        return $this->studyGroups;
    }
}