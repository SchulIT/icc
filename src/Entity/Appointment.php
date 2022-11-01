<?php

namespace App\Entity;

use DateInterval;
use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 */
class Appointment {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private ?string $externalId = null;

    /**
     * @ORM\Column(type="string")
     */
    #[Assert\NotNull]
    #[Assert\NotBlank]
    private ?string $title = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $content = null;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Assert\NotNull]
    private ?\DateTime $start = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    #[Assert\GreaterThan(propertyPath: 'start')]
    private ?\DateTime $end = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $location = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $allDay = true;

    /**
     * @ORM\ManyToMany(targetEntity="StudyGroup", cascade={"persist"})
     * @ORM\JoinTable(name="appointment_studygroups",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var ArrayCollection<StudyGroup>
     */
    private $studyGroups;

    /**
     * @ORM\ManyToMany(targetEntity="Teacher", cascade={"persist"})
     * @ORM\JoinTable(name="appointment_organizers",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var ArrayCollection<Teacher>
     */
    private $organizers;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $externalOrganizers = null;

    /**
     * @ORM\ManyToOne(targetEntity="AppointmentCategory")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    #[Assert\NotNull]
    private ?AppointmentCategory $category = null;

    /**
     * @ORM\ManyToMany(targetEntity="UserTypeEntity")
     * @ORM\JoinTable(name="appointment_visibilities",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var ArrayCollection<UserTypeEntity>
     */
    private $visibilities;

    /**
     * @ORM\Column(type="boolean", options={"default": true})
     */
    private bool $isConfirmed = true;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=true)
     *
     * Note: we cannot use the Blameable() listener here as it would break when importing appointments
     * from API
     */
    private ?User $createdBy = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();

        $this->studyGroups = new ArrayCollection();
        $this->organizers = new ArrayCollection();
        $this->visibilities = new ArrayCollection();
    }

    public function getExternalId(): ?string {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): Appointment {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(?string $title): Appointment {
        $this->title = $title;
        return $this;
    }

    public function getContent(): ?string {
        return $this->content;
    }

    public function setContent(?string $content): Appointment {
        $this->content = $content;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStart(): ?DateTime {
        return $this->start;
    }

    /**
     * @param DateTime $start
     */
    public function setStart(?DateTime $start): Appointment {
        $this->start = $start;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEnd(): ?DateTime {
        return $this->end;
    }

    /**
     * @param DateTime $end
     */
    public function setEnd(?DateTime $end): Appointment {
        $this->end = $end;
        return $this;
    }

    public function getLocation(): ?string {
        return $this->location;
    }

    public function setLocation(?string $location): Appointment {
        $this->location = $location;
        return $this;
    }

    public function isAllDay(): bool {
        return $this->allDay;
    }

    public function setAllDay(bool $allDay): Appointment {
        $this->allDay = $allDay;
        return $this;
    }

    public function getCategory(): ?AppointmentCategory {
        return $this->category;
    }

    public function setCategory(AppointmentCategory $category): Appointment {
        $this->category = $category;
        return $this;
    }

    public function getExternalOrganizers(): ?string {
        return $this->externalOrganizers;
    }

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

    public function addVisibility(UserTypeEntity $visibility) {
        $this->visibilities->add($visibility);
    }

    public function removeVisibility(UserTypeEntity $visibility) {
        $this->visibilities->removeElement($visibility);
    }

    /**
     * @return Collection<UserTypeEntity>
     */
    public function getVisibilities(): Collection {
        return $this->visibilities;
    }

    public function isConfirmed(): bool {
        return $this->isConfirmed;
    }

    public function setIsConfirmed(bool $isConfirmed): Appointment {
        $this->isConfirmed = $isConfirmed;
        return $this;
    }

    public function getCreatedBy(): ?User {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): Appointment {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getRealEnd(): DateTime {
        if($this->isAllDay() === false) {
            return $this->getEnd();
        }

        return (clone $this->getEnd())->modify('-1 second');
    }

    public function getDuration(): DateInterval {
        return $this->getStart()->diff($this->getEnd());
    }
}