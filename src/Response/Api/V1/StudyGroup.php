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
     */
    private ?string $name = null;

    /**
     * @Serializer\SerializedName("type")
     * @Serializer\Type("string")
     * @Serializer\ReadOnly()
     * @Serializer\Accessor(getter="getTypeString")
     */
    private ?StudyGroupType $type = null;

    /**
     * @Serializer\SerializedName("grades")
     * @Serializer\Type("array<App\Response\Api\V1\Grade>")
     * @var Grade[]
     */
    private ?array $grades = null;

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): StudyGroup {
        $this->name = $name;
        return $this;
    }

    public function getType(): StudyGroupType {
        return $this->type;
    }

    public function setType(StudyGroupType $type): StudyGroup {
        $this->type = $type;
        return $this;
    }

    public function getTypeString(): string {
        return $this->type->value;
    }

    /**
     * @return Grade[]
     */
    public function getGrades(): array {
        return $this->grades;
    }

    /**
     * @param Grade[] $grades
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
            ->setGrades(array_map(fn(GradeEntity $gradeEntity) => Grade::fromEntity($gradeEntity), $studyGroupEntity->getGrades()->toArray()));
    }
}