<?php

namespace App\Request\Data;

use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class AppointmentData {

    /**
     * Your ID which is used to update existing appointments.
     *
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private string $id;

    /**
     * The key of the category which the appointment belongs to.
     *
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private string $category;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private string $subject;

    /**
     * Content of the appointment - must not be empty but may be null.
     *
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $content = null;

    /**
     * @Serializer\Type("DateTime")
     */
    #[Assert\NotNull]
    private DateTime $start;

    /**
     * End of the appointment. Note: this value is exclusive which means that an all day appointment on April 30, 2020
     * has a start date of "2020-04-30T00:00:00" and end date of "2020-05-01T00:00:00".
     *
     * @Serializer\Type("DateTime")
     */
    #[Assert\NotNull]
    private DateTime $end;

    /**
     * Location of the appointment - must not be empty but may be null.
     *
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $location = null;

    /**
     * @Serializer\Type("boolean")
     */
    private bool $isAllDay;

    /**
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    #[Assert\Type('array')]
    #[Assert\Choice(['student', 'parent', 'teacher'], multiple: true)]
    private ?array $visibilities = null;

    /**
     * List of external study group IDs, which this appointment belongs to. May be empty.
     *
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private array $studyGroups;

    /**
     * @deprecated This property is ignored when importing
     * @Serializer\Type("bool")
     * @Serializer\SerializedName("mark_students_absent")
     * Whether or not to mark students absent during this appointment
     */
    private bool $markStudentsAbsent = true;

    /**
     * List of teachers (their acronyms) which attend this appointment.
     *
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private array $organizers;

    /**
     * List of external organizers - must not be empty but may be null.
     *
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $externalOrganizers = null;

    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id): AppointmentData {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory($category): AppointmentData {
        $this->category = $category;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubject() {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject): AppointmentData {
        $this->subject = $subject;
        return $this;
    }

    public function getContent(): ?string {
        return $this->content;
    }

    public function setContent(?string $content): AppointmentData {
        $this->content = $content;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStart() {
        return $this->start;
    }

    /**
     * @param \DateTime $start
     */
    public function setStart($start): AppointmentData {
        $this->start = $start;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEnd() {
        return $this->end;
    }

    /**
     * @param \DateTime $end
     */
    public function setEnd($end): AppointmentData {
        $this->end = $end;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * @param string|null $location
     */
    public function setLocation($location): AppointmentData {
        $this->location = $location;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAllDay() {
        return $this->isAllDay;
    }

    /**
     * @param bool $isAllDay
     */
    public function setIsAllDay($isAllDay): AppointmentData {
        $this->isAllDay = $isAllDay;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getVisibilities(): array {
        return $this->visibilities;
    }

    /**
     * @param string[] $visibilities
     */
    public function setVisibilities(array $visibilities): AppointmentData {
        $this->visibilities = $visibilities;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getStudyGroups() {
        return $this->studyGroups;
    }

    /**
     * @param string[] $studyGroups
     */
    public function setStudyGroups($studyGroups): AppointmentData {
        $this->studyGroups = $studyGroups;
        return $this;
    }

    public function isMarkStudentsAbsent(): bool {
        return $this->markStudentsAbsent;
    }

    public function setMarkStudentsAbsent(bool $markStudentsAbsent): AppointmentData {
        $this->markStudentsAbsent = $markStudentsAbsent;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getOrganizers() {
        return $this->organizers;
    }

    /**
     * @param string[] $organizers
     */
    public function setOrganizers($organizers): AppointmentData {
        $this->organizers = $organizers;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getExternalOrganizers() {
        return $this->externalOrganizers;
    }

    /**
     * @param string|null $externalOrganizers
     */
    public function setExternalOrganizers($externalOrganizers): AppointmentData {
        $this->externalOrganizers = $externalOrganizers;
        return $this;
    }

}