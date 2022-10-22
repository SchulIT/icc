<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class GradeData {

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $id = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $name = null;

    public function setId(?string $id): GradeData {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?string {
        return $this->id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): GradeData {
        $this->name = $name;
        return $this;
    }
}