<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class SubjectData {

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $id;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $abbreviation;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $name;

    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param string $id
     * @return SubjectData
     */
    public function setId($id): SubjectData {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAbbreviation(): ?string {
        return $this->abbreviation;
    }

    /**
     * @param string|null $abbreviation
     * @return SubjectData
     */
    public function setAbbreviation(?string $abbreviation): SubjectData {
        $this->abbreviation = $abbreviation;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return SubjectData
     */
    public function setName(?string $name): SubjectData {
        $this->name = $name;
        return $this;
    }
}