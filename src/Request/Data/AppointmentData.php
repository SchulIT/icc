<?php

namespace App\Request\Data;

use App\Validator\NullOrNotBlank;
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
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $content;

    /**
     * @Serializer\Type("datetime")
     * @Assert\DateTime()
     * @Assert\NotNull()
     * @var \DateTime
     */
    private $start;

    /**
     * @Serializer\Type("datetime")
     * @Assert\DateTime()
     * @Assert\NotNull()
     * @var \DateTime
     */
    private $end;

    /**
     * @Serializer\Type("string")
     * @NullOrNotBlank()
     * @var string|null
     */
    private $location;

    /**
     * @Serializer\Type("bool")
     * @var boolean
     */
    private $isAllDay;

    /**
     * @Serializer\Type("bool")
     * @var boolean
     */
    private $isHiddenFromStudents;

    /**
     * List of external study group IDs, which this appointment belongs to.
     *
     * @Serializer\Type("array<int>")
     * @var int[]
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
     * @Serializer\Type("string")
     * @NullOrNotBlank()
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
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @param string $content
     * @return AppointmentData
     */
    public function setContent($content): AppointmentData {
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
     * @return bool
     */
    public function isHiddenFromStudents() {
        return $this->isHiddenFromStudents;
    }

    /**
     * @param bool $isHiddenFromStudents
     * @return AppointmentData
     */
    public function setIsHiddenFromStudents($isHiddenFromStudents): AppointmentData {
        $this->isHiddenFromStudents = $isHiddenFromStudents;
        return $this;
    }

    /**
     * @return int[]
     */
    public function getStudyGroups() {
        return $this->studyGroups;
    }

    /**
     * @param int[] $studyGroups
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