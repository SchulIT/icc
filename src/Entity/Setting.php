<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[UniqueEntity(fields: ['key'])]
#[ORM\Entity]
class Setting {

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Id]
    #[ORM\Column(name: '`key`', type: 'string', unique: true)]
    private ?string $key = null;

    /**
     * @var mixed
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private mixed $value = null;

    public function getKey(): string {
        return $this->key;
    }

    public function setKey(string $key): Setting {
        $this->key = $key;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue(): mixed {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return Setting
     */
    public function setValue(mixed $value): self {
        $this->value = $value;
        return $this;
    }
}