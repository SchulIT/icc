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
     *
     * @var DateTimeInterface
     */
    private $date;

    /**
     * @Serializer\SerializedName("lesson_start")
     * @Serializer\Type("int")
     *
     * @var int
     */
    private $lessonStart;

    /**
     * @Serializer\SerializedName("lesson_end")
     * @Serializer\Type("int")
     *
     * @var int
     */
    private $lessonEnd;

    /**
     * @Serializer\SerializedName("starts_before")
     * @Serializer\Type("bool")
     *
     * @var bool
     */
    private $startsBefore;

    /**
     * @Serializer\SerializedName("type")
     * @Serializer\Type("string")
     *
     * @var string|null
     */
    private $type;

    /**
     * @Serializer\SerializedName("subject")
     * @Serializer\Type("string")
     *
     * @var string|null
     */
    private $subject;

    /**
     * @Serializer\SerializedName("replacement_subject")
     * @Serializer\Type("string")
     *
     * @var string|null
     */
    private $replacementSubject;

    /**
     * @Serializer\SerializedName("teachers")
     * @Serializer\Type("array<App\Response\Api\V1\Teacher>")
     *
     * @var Teacher[]
     */
    private $teachers;

    /**
     * @Serializer\SerializedName("replacement_teachers")
     * @Serializer\Type("array<App\Response\Api\V1\Teacher>")
     *
     * @var Teacher[]
     */
    private $replacementTeachers;

    /**
     * @Serializer\SerializedName("room")
     * @Serializer\Type("string")
     *
     * @var string|null
     */
    private $room;

    /**
     * @Serializer\SerializedName("replacement_room")
     * @Serializer\Type("string")
     *
     * @var string|null
     */
    private $replacementRoom;

    /**
     * @Serializer\SerializedName("remark")
     * @Serializer\Type("string")
     *
     * @var string|null
     */
    private $remark;

    /**
     * @Serializer\SerializedName("study_groups")
     * @Serializer\Type("array<App\Response\Api\V1\StudyGroup>")
     *
     * @var StudyGroup[]
     */
    private $studyGroups;

    /**
     * @Serializer\SerializedName("replacement_study_groups")
     * @Serializer\Type("array<App\Response\Api\V1\StudyGroup>")
     *
     * @var StudyGroup[]
     */
    private $replacementStudyGroups;

    /**
     * @return DateTimeInterface
     */
    public function getDate(): DateTimeInterface {
        return $this->date;
    }

    /**
     * @param DateTimeInterface $date
     * @return Substitution
     */
    public function setDate(DateTimeInterface $date): Substitution {
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
     * @return bool
     */
    public function isStartsBefore(): bool {
        return $this->startsBefore;
    }

    /**
     * @param bool $startsBefore
     * @return Substitution
     */
    public function setStartsBefore(bool $startsBefore): Substitution {
        $this->startsBefore = $startsBefore;
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
     * @return Teacher[]
     */
    public function getTeachers(): array {
        return $this->teachers;
    }

    /**
     * @param Teacher[] $teachers
     * @return Substitution
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
     * @return Substitution
     */
    public function setReplacementTeachers(array $replacementTeachers): Substitution {
        $this->replacementTeachers = $replacementTeachers;
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
     * @return string|null
     */
    public function getRemark(): ?string {
        return $this->remark;
    }

    /**
     * @param string|null $remark
     * @return Substitution
     */
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
     * @return Substitution
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
     * @return Substitution
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
            ->setTeachers(array_map(function(TeacherEntity $teacherEntity) {
                return Teacher::fromEntity($teacherEntity);
            }, $substitutionEntity->getTeachers()->toArray()))
            ->setReplacementTeachers(array_map(function(TeacherEntity $teacherEntity) {
                return Teacher::fromEntity($teacherEntity);
            }, $substitutionEntity->getTeachers()->toArray()))
            ->setRoom($substitutionEntity->getRoom() !== null ? $substitutionEntity->getRoom()->getName() : $substitutionEntity->getRoomName())
            ->setReplacementRoom($substitutionEntity->getReplacementRoom() !== null ? $substitutionEntity->getReplacementRoom()->getName() : $substitutionEntity->getReplacementRoomName())
            ->setRemark($substitutionEntity->getRemark())
            ->setStudyGroups(array_map(function(StudyGroupEntity $studyGroupEntity) {
                return StudyGroup::fromEntity($studyGroupEntity);
            }, $substitutionEntity->getStudyGroups()->toArray()))
            ->setReplacementStudyGroups(array_map(function (StudyGroupEntity $studyGroupEntity) {
                return StudyGroup::fromEntity($studyGroupEntity);
            }, $substitutionEntity->getReplacementStudyGroups()->toArray()));
    }
}