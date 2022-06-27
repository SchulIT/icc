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
     * @var string
     */
    private $name;

    /**
     * @Serializer\SerializedName("subject")
     * @Serializer\Type("App\Response\Api\V1\Subject")
     * @var Subject
     */
    private $subject;

    /**
     * @Serializer\SerializedName("teachers")
     * @Serializer\Type("array<App\Response\Api\V1\Teacher>")
     * @var Teacher[]
     */
    private $teachers;

    /**
     * @Serializer\SerializedName("study_group")
     * @Serializer\Type("App\Response\Api\V1\StudyGroup")
     *
     * @var StudyGroup
     */
    private $studyGroup;

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Tuition
     */
    public function setName(string $name): Tuition {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Subject
     */
    public function getSubject(): Subject {
        return $this->subject;
    }

    /**
     * @param Subject $subject
     * @return Tuition
     */
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
     * @return Tuition
     */
    public function setTeachers(array $teachers): Tuition {
        $this->teachers = $teachers;
        return $this;
    }

    /**
     * @return StudyGroup
     */
    public function getStudyGroup(): StudyGroup {
        return $this->studyGroup;
    }

    /**
     * @param StudyGroup $studyGroup
     * @return Tuition
     */
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
            ->setTeachers(array_map(function(TeacherEntity $teacher) {
                return Teacher::fromEntity($teacher);
            }, $tuitionEntity->getTeachers()->toArray()))
            ->setUuid($tuitionEntity->getUuid());
    }
}