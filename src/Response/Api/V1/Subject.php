<?php

namespace App\Response\Api\V1;

use App\Entity\Subject as SubjectEntity;
use JMS\Serializer\Annotation as Serializer;

class Subject {

    use UuidTrait;

    /**
     * @Serializer\SerializedName("abbreviation")
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $abbreviation;

    /**
     * @Serializer\SerializedName("name")
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $name;

    /**
     * @return string
     */
    public function getAbbreviation(): string {
        return $this->abbreviation;
    }

    /**
     * @param string $abbreviation
     * @return Subject
     */
    public function setAbbreviation(string $abbreviation): Subject {
        $this->abbreviation = $abbreviation;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Subject
     */
    public function setName(string $name): Subject {
        $this->name = $name;
        return $this;
    }

    public static function fromEntity(?SubjectEntity $subjectEntity): ?self {
        if($subjectEntity === null) {
            return null;
        }

        return (new self())
            ->setName($subjectEntity->getName())
            ->setUuid($subjectEntity->getUuid())
            ->setAbbreviation($subjectEntity->getAbbreviation());
    }
}