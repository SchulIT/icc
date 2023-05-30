<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class GradeData {

    #[Assert\NotBlank]
    #[Serializer\Type('string')]
    private ?string $id = null;

    #[Assert\NotBlank]
    #[Serializer\Type('string')]
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