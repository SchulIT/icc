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
    #[ORM\Id]
    #[ORM\Column(name: '`key`', type: 'string', unique: true)]
    private ?string $key = null;

    /**
     * @var mixed
     */
    #[ORM\Column(type: 'object')]
    private $value = null;

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
    public function getValue() {
        return $this->value;
    }

    /**
     * @return Setting
     */
    public function setValue(mixed $value) {
        $this->value = $value;
        return $this;
    }
}