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
     */
    private ?string $title = null;

    /**
     * May be null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("content")
     */
    private ?string $content = null;

    /**
     * @Serializer\Type("DateTime")
     * @Serializer\SerializedName("start")
     */
    private ?\DateTime $start = null;

    /**
     * @Serializer\Type("DateTime")
     * @Serializer\SerializedName("end")
     */
    private ?\DateTime $end = null;

    /**
     * May be null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("location")
     */
    private ?string $location = null;

    /**
     * @Serializer\Type("bool")
     * @Serializer\SerializedName("all_day")
     */
    private ?bool $allDay = null;

    /**
     * @Serializer\Type("array<App\Response\Api\V1\StudyGroup>")
     * @Serializer\SerializedName("study_groups")
     * @var StudyGroup[]
     */
    private ?array $studyGroups = null;

    /**
     * @Serializer\Type("array<App\Response\Api\V1\Teacher>")
     * @Serializer\SerializedName("organizers")
     * @var Teacher[]
     */
    private ?array $organizers = null;

    /**
     * May be null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("external_organizers")
     */
    private ?string $externalOrganizers = null;

    /**
     * @Serializer\Type("App\Response\Api\V1\AppointmentCategory")
     * @Serializer\SerializedName("category")
     */
    private ?AppointmentCategory $category = null;

    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $title): Appointment {
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

    public function getStart(): DateTime {
        return $this->start;
    }

    public function setStart(DateTime $start): Appointment {
        $this->start = $start;
        return $this;
    }

    public function getEnd(): DateTime {
        return $this->end;
    }

    public function setEnd(DateTime $end): Appointment {
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

    /**
     * @return StudyGroup[]
     */
    public function getStudyGroups(): array {
        return $this->studyGroups;
    }

    /**
     * @param StudyGroup[] $studyGroups
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
     */
    public function setOrganizers(array $organizers): Appointment {
        $this->organizers = $organizers;
        return $this;
    }

    public function getExternalOrganizers(): ?string {
        return $this->externalOrganizers;
    }

    public function setExternalOrganizers(?string $externalOrganizers): Appointment {
        $this->externalOrganizers = $externalOrganizers;
        return $this;
    }

    public function getCategory(): AppointmentCategory {
        return $this->category;
    }

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
            ->setStudyGroups(array_map(fn(StudyGroupEntity $studyGroup) => StudyGroup::fromEntity($studyGroup), $entity->getStudyGroups()->toArray()))
            ->setOrganizers(array_map(fn(TeacherEntity $teacher) => Teacher::fromEntity($teacher), $entity->getOrganizers()->toArray()))
            ->setExternalOrganizers($entity->getExternalOrganizers())
            ->setCategory(AppointmentCategory::fromEntity($entity->getCategory()));
    }
}