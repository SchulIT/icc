<?php

namespace App\Response\Api\V1;

use App\Entity\StudyGroup as StudyGroupEntity;
use App\Entity\Substitution as SubstitutionEntity;
use App\Entity\Teacher as TeacherEntity;
use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;

class Substitution {

    use UuidTrait;

    /**
     * @Serializer\SerializedName("date")
     * @Serializer\Type("DateTime")
     */
    private ?DateTimeInterface $date = null;

    /**
     * @Serializer\SerializedName("lesson_start")
     * @Serializer\Type("int")
     */
    private ?int $lessonStart = null;

    /**
     * @Serializer\SerializedName("lesson_end")
     * @Serializer\Type("int")
     */
    private ?int $lessonEnd = null;

    /**
     * @Serializer\SerializedName("starts_before")
     * @Serializer\Type("bool")
     */
    private ?bool $startsBefore = null;

    /**
     * @Serializer\SerializedName("type")
     * @Serializer\Type("string")
     */
    private ?string $type = null;

    /**
     * @Serializer\SerializedName("subject")
     * @Serializer\Type("string")
     */
    private ?string $subject = null;

    /**
     * @Serializer\SerializedName("replacement_subject")
     * @Serializer\Type("string")
     */
    private ?string $replacementSubject = null;

    /**
     * @Serializer\SerializedName("teachers")
     * @Serializer\Type("array<App\Response\Api\V1\Teacher>")
     *
     * @var Teacher[]
     */
    private ?array $teachers = null;

    /**
     * @Serializer\SerializedName("replacement_teachers")
     * @Serializer\Type("array<App\Response\Api\V1\Teacher>")
     *
     * @var Teacher[]
     */
    private ?array $replacementTeachers = null;

    /**
     * @Serializer\SerializedName("room")
     * @Serializer\Type("string")
     */
    private ?string $room = null;

    /**
     * @Serializer\SerializedName("replacement_room")
     * @Serializer\Type("string")
     */
    private ?string $replacementRoom = null;

    /**
     * @Serializer\SerializedName("remark")
     * @Serializer\Type("string")
     */
    private ?string $remark = null;

    /**
     * @Serializer\SerializedName("study_groups")
     * @Serializer\Type("array<App\Response\Api\V1\StudyGroup>")
     *
     * @var StudyGroup[]
     */
    private ?array $studyGroups = null;

    /**
     * @Serializer\SerializedName("replacement_study_groups")
     * @Serializer\Type("array<App\Response\Api\V1\StudyGroup>")
     *
     * @var StudyGroup[]
     */
    private ?array $replacementStudyGroups = null;

    public function getDate(): DateTimeInterface {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): Substitution {
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

    public function isStartsBefore(): bool {
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
     * @return Teacher[]
     */
    public function getTeachers(): array {
        return $this->teachers;
    }

    /**
     * @param Teacher[] $teachers
     */
    public function setTeachers(array $teachers): Substitution {
        $this->teachers = $teachers;
        return $this;
    }

    /**
     * @return Teacher[]
     */
    public function getReplacementTeachers(): array {
        return $this->replacementTeachers;
    }

    /**
     * @param Teacher[] $replacementTeachers
     */
    public function setReplacementTeachers(array $replacementTeachers): Substitution {
        $this->replacementTeachers = $replacementTeachers;
        return $this;
    }

    public function getRoom(): ?string {
        return $this->room;
    }

    public function setRoom(?string $room): Substitution {
        $this->room = $room;
        return $this;
    }

    public function getReplacementRoom(): ?string {
        return $this->replacementRoom;
    }

    public function setReplacementRoom(?string $replacementRoom): Substitution {
        $this->replacementRoom = $replacementRoom;
        return $this;
    }

    public function getRemark(): ?string {
        return $this->remark;
    }

    public function setRemark(?string $remark): Substitution {
        $this->remark = $remark;
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
    public function setStudyGroups(array $studyGroups): Substitution {
        $this->studyGroups = $studyGroups;
        return $this;
    }

    /**
     * @return StudyGroup[]
     */
    public function getReplacementStudyGroups(): array {
        return $this->replacementStudyGroups;
    }

    /**
     * @param StudyGroup[] $replacementStudyGroups
     */
    public function setReplacementStudyGroups(array $replacementStudyGroups): Substitution {
        $this->replacementStudyGroups = $replacementStudyGroups;
        return $this;
    }

    public static function fromEntity(SubstitutionEntity $substitutionEntity): self {
        return (new self())
            ->setUuid($substitutionEntity->getUuid())
            ->setDate($substitutionEntity->getDate())
            ->setLessonStart($substitutionEntity->getLessonStart())
            ->setLessonEnd($substitutionEntity->getLessonEnd())
            ->setStartsBefore($substitutionEntity->startsBefore())
            ->setType($substitutionEntity->getType())
            ->setSubject($substitutionEntity->getSubject())
            ->setReplacementSubject($substitutionEntity->getReplacementSubject())
            ->setTeachers(array_map(fn(TeacherEntity $teacherEntity) => Teacher::fromEntity($teacherEntity), $substitutionEntity->getTeachers()->toArray()))
            ->setReplacementTeachers(array_map(fn(TeacherEntity $teacherEntity) => Teacher::fromEntity($teacherEntity), $substitutionEntity->getTeachers()->toArray()))
            ->setRoom($substitutionEntity->getRoomsAsString())
            ->setReplacementRoom($substitutionEntity->getReplacementRoomsAsString())
            ->setRemark($substitutionEntity->getRemark())
            ->setStudyGroups(array_map(fn(StudyGroupEntity $studyGroupEntity) => StudyGroup::fromEntity($studyGroupEntity), $substitutionEntity->getStudyGroups()->toArray()))
            ->setReplacementStudyGroups(array_map(fn(StudyGroupEntity $studyGroupEntity) => StudyGroup::fromEntity($studyGroupEntity), $substitutionEntity->getReplacementStudyGroups()->toArray()));
    }
}