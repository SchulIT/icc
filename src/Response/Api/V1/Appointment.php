<?php

namespace App\Response\Api\V1;

use App\Entity\Appointment as AppointmentEntity;
use App\Entity\StudyGroup as StudyGroupEntity;
use App\Entity\Teacher as TeacherEntity;
use DateTime;
use JMS\Serializer\Annotation as Serializer;

class Appointment {

    use UuidTrait;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("title")
     * @var string
     */
    private $title;

    /**
     * May be null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("content")
     * @var string|null
     */
    private $content;

    /**
     * @Serializer\Type("DateTime")
     * @Serializer\SerializedName("start")
     * @var DateTime
     */
    private $start;

    /**
     * @Serializer\Type("DateTime")
     * @Serializer\SerializedName("end")
     * @var DateTime
     */
    private $end;

    /**
     * May be null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("location")
     * @var string|null
     */
    private $location;

    /**
     * @Serializer\Type("bool")
     * @Serializer\SerializedName("all_day")
     * @var bool
     */
    private $allDay;

    /**
     * @Serializer\Type("array<App\Response\Api\V1\StudyGroup>")
     * @Serializer\SerializedName("study_groups")
     * @var StudyGroup[]
     */
    private $studyGroups;

    /**
     * @Serializer\Type("array<App\Response\Api\V1\Teacher>")
     * @Serializer\SerializedName("organizers")
     * @var Teacher[]
     */
    private $organizers;

    /**
     * May be null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("external_organizers")
     * @var string|null
     */
    private $externalOrganizers;

    /**
     * @Serializer\Type("App\Response\Api\V1\AppointmentCategory")
     * @Serializer\SerializedName("category")
     * @var AppointmentCategory
     */
    private $category;

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
     * @return DateTime
     */
    public function getStart(): DateTime {
        return $this->start;
    }

    /**
     * @param DateTime $start
     * @return Appointment
     */
    public function setStart(DateTime $start): Appointment {
        $this->start = $start;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEnd(): DateTime {
        return $this->end;
    }

    /**
     * @param DateTime $end
     * @return Appointment
     */
    public function setEnd(DateTime $end): Appointment {
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
     * @return StudyGroup[]
     */
    public function getStudyGroups(): array {
        return $this->studyGroups;
    }

    /**
     * @param StudyGroup[] $studyGroups
     * @return Appointment
     */
    public function setStudyGroups(array $studyGroups): Appointment {
        $this->studyGroups = $studyGroups;
        return $this;
    }

    /**
     * @return Teacher[]
     */
    public function getOrganizers(): array {
        return $this->organizers;
    }

    /**
     * @param Teacher[] $organizers
     * @return Appointment
     */
    public function setOrganizers(array $organizers): Appointment {
        $this->organizers = $organizers;
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

    /**
     * @return AppointmentCategory
     */
    public function getCategory(): AppointmentCategory {
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

    public static function fromEntity(AppointmentEntity $entity): self {
        return (new self())
            ->setUuid($entity->getUuid())
            ->setTitle($entity->getTitle())
            ->setContent($entity->getContent())
            ->setStart($entity->getStart())
            ->setEnd($entity->getEnd())
            ->setLocation($entity->getLocation())
            ->setAllDay($entity->isAllDay())
            ->setStudyGroups(array_map(function(StudyGroupEntity $studyGroup) {
                return StudyGroup::fromEntity($studyGroup);
            }, $entity->getStudyGroups()->toArray()))
            ->setOrganizers(array_map(function(TeacherEntity $teacher) {
                return Teacher::fromEntity($teacher);
            }, $entity->getOrganizers()->toArray()))
            ->setExternalOrganizers($entity->getExternalOrganizers())
            ->setCategory(AppointmentCategory::fromEntity($entity->getCategory()));
    }
}