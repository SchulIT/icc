<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class LearningManagementSystemData {

    #[Serializer\Type('string')]
    #[Assert\NotBlank]
    private ?string $id;

    #[Serializer\Type('string')]
    #[Assert\NotBlank]
    private ?string $name;

    /**
     * @return string|null
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * @param string|null $id
     * @return LearningManagementSystemData
     */
    public function setId(?string $id): LearningManagementSystemData {
        $this->id = $id;
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
     * @return LearningManagementSystemData
     */
    public function setName(?string $name): LearningManagementSystemData {
        $this->name = $name;
        return $this;
    }
}