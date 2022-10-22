<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class SubjectData {

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private string $id;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $abbreviation = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $name = null;

    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id): SubjectData {
        $this->id = $id;
        return $this;
    }

    public function getAbbreviation(): ?string {
        return $this->abbreviation;
    }

    public function setAbbreviation(?string $abbreviation): SubjectData {
        $this->abbreviation = $abbreviation;
        return $this;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): SubjectData {
        $this->name = $name;
        return $this;
    }
}