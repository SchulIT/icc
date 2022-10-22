<?php

namespace App\Response\Api\V1;

use JMS\Serializer\Annotation as Serializer;
use App\Entity\Grade as GradeEntity;

class Grade {

    use UuidTrait;

    /**
     * @Serializer\SerializedName("name")
     * @Serializer\Type("string")
     */
    private ?string $name = null;

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): Grade {
        $this->name = $name;
        return $this;
    }

    public static function fromEntity(?GradeEntity $gradeEntity): ?self {
        if($gradeEntity === null) {
            return null;
        }

        return (new self())
            ->setName($gradeEntity->getName())
            ->setUuid($gradeEntity->getUuid());
    }
}