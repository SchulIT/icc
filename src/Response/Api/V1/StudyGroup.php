<?php

namespace App\Response\Api\V1;

use App\Entity\Grade as GradeEntity;
use App\Entity\StudyGroup as StudyGroupEntity;
use App\Entity\StudyGroupType;
use JMS\Serializer\Annotation as Serializer;

class StudyGroup {

    use UuidTrait;

    /**
     * @Serializer\SerializedName("name")
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $name;

    /**
     * @Serializer\SerializedName("type")
     * @Serializer\Type("string")
     * @Serializer\ReadOnly()
     * @Serializer\Accessor(getter="getTypeString")
     *
     * @var StudyGroupType
     */
    private $type;

    /**
     * @Serializer\SerializedName("grades")
     * @Serializer\Type("array<App\Response\Api\V1\Grade>")
     * @var Grade[]
     */
    private $grades;

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return StudyGroup
     */
    public function setName(string $name): StudyGroup {
        $this->name = $name;
        return $this;
    }

    /**
     * @return StudyGroupType
     */
    public function getType(): StudyGroupType {
        return $this->type;
    }

    /**
     * @param StudyGroupType $type
     * @return StudyGroup
     */
    public function setType(StudyGroupType $type): StudyGroup {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getTypeString(): string {
        return $this->type->getValue();
    }

    /**
     * @return Grade[]
     */
    public function getGrades(): array {
        return $this->grades;
    }

    /**
     * @param Grade[] $grades
     * @return StudyGroup
     */
    public function setGrades(array $grades): StudyGroup {
        $this->grades = $grades;
        return $this;
    }

    public static function fromEntity(?StudyGroupEntity $studyGroupEntity): ?self {
        if($studyGroupEntity === null) {
            return null;
        }

        return (new self())
            ->setUuid($studyGroupEntity->getUuid())
            ->setName($studyGroupEntity->getName())
            ->setType($studyGroupEntity->getType())
            ->setGrades(array_map(function(GradeEntity $gradeEntity) {
                return Grade::fromEntity($gradeEntity);
            }, $studyGroupEntity->getGrades()->toArray()));
    }
}