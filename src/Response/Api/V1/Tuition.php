<?php

namespace App\Response\Api\V1;

use App\Entity\Tuition as TuitionEntity;
use App\Entity\Teacher as TeacherEntity;
use JMS\Serializer\Annotation as Serializer;

class Tuition {

    use UuidTrait;

    /**
     * @Serializer\SerializedName("name")
     * @Serializer\Type("string")
     */
    private ?string $name = null;

    /**
     * @Serializer\SerializedName("subject")
     * @Serializer\Type("App\Response\Api\V1\Subject")
     */
    private ?Subject $subject = null;

    /**
     * @Serializer\SerializedName("teachers")
     * @Serializer\Type("array<App\Response\Api\V1\Teacher>")
     * @var Teacher[]
     */
    private ?array $teachers = null;

    /**
     * @Serializer\SerializedName("study_group")
     * @Serializer\Type("App\Response\Api\V1\StudyGroup")
     */
    private ?StudyGroup $studyGroup = null;

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): Tuition {
        $this->name = $name;
        return $this;
    }

    public function getSubject(): Subject {
        return $this->subject;
    }

    public function setSubject(Subject $subject): Tuition {
        $this->subject = $subject;
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
    public function setTeachers(array $teachers): Tuition {
        $this->teachers = $teachers;
        return $this;
    }

    public function getStudyGroup(): StudyGroup {
        return $this->studyGroup;
    }

    public function setStudyGroup(StudyGroup $studyGroup): Tuition {
        $this->studyGroup = $studyGroup;
        return $this;
    }

    public static function fromEntity(?TuitionEntity $tuitionEntity): ?self {
        if($tuitionEntity === null) {
            return null;
        }

        return (new self())
            ->setName($tuitionEntity->getName())
            ->setStudyGroup(StudyGroup::fromEntity($tuitionEntity->getStudyGroup()))
            ->setSubject(Subject::fromEntity($tuitionEntity->getSubject()))
            ->setTeachers(array_map(fn(TeacherEntity $teacher) => Teacher::fromEntity($teacher), $tuitionEntity->getTeachers()->toArray()))
            ->setUuid($tuitionEntity->getUuid());
    }
}