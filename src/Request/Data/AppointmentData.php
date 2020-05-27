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
     * @Assert\NotBlank()
     * @var string
     */
    private $id;

    /**
     * The key of the category which the appointment belongs to.
     *
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $category;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $subject;

    /**
     * Content of the appointment - must not be empty but may be null.
     *
     * @Serializer\Type("string")
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private $content;

    /**
     * @Serializer\Type("datetime")
     * @Assert\NotNull()
     * @var DateTime
     */
    private $start;

    /**
     * @Serializer\Type("datetime")
     * @Assert\NotNull()
     * @var DateTime
     */
    private $end;

    /**
     * Location of the appointment - must not be empty but may be null.
     *
     * @Serializer\Type("string")
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private $location;

    /**
     * @Serializer\Type("boolean")
     * @var boolean
     */
    private $isAllDay;

    /**
     * @Serializer\Type("array<string>")
     * @Assert\Type("array")
     * @Assert\Choice({"student", "parent", "teacher"}, multiple=true)
     * @var string[]
     */
    private $visibilities;

    /**
     * List of external study group IDs, which this appointment belongs to. May be empty.
     *
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private $studyGroups;

    /**
     * List of teachers (their acronyms) which attend this appointment.
     *
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private $organizers;

    /**
     * List of external organizers - must not be empty but may be null.
     *
     * @Serializer\Type("string")
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private $externalOrganizers;

    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param string $id
     * @return AppointmentData
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
     * @return AppointmentData
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
     * @return AppointmentData
     */
    public function setSubject($subject): AppointmentData {
        $this->subject = $subject;
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
     * @return AppointmentData
     */
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
     * @return AppointmentData
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
     * @return AppointmentData
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
     * @return AppointmentData
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
     * @return AppointmentData
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
     * @return AppointmentData
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
     * @return AppointmentData
     */
    public function setStudyGroups($studyGroups): AppointmentData {
        $this->studyGroups = $studyGroups;
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
     * @return AppointmentData
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
     * @return AppointmentData
     */
    public function setExternalOrganizers($externalOrganizers): AppointmentData {
        $this->externalOrganizers = $externalOrganizers;
        return $this;
    }

}