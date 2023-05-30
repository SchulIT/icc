<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class LearningManagementSystemData {

    #[Serializer\Type('string')]
    #[Assert\NotBlank]
    private ?string $id = null;

    #[Serializer\Type('string')]
    #[Assert\NotBlank]
    private ?string $name = null;

    public function getId(): ?string {
        return $this->id;
    }

    public function setId(?string $id): LearningManagementSystemData {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): LearningManagementSystemData {
        $this->name = $name;
        return $this;
    }
}