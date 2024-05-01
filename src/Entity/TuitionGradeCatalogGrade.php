<?php

namespace App\Entity;

use App\Validator\Color;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class TuitionGradeCatalogGrade {

    use IdTrait;

    #[ORM\ManyToOne(targetEntity: TuitionGradeCatalog::class, inversedBy: 'values')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private TuitionGradeCatalog $catalog;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private ?string $value = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    #[Color]
    private ?string $color = null;

    public function getCatalog(): TuitionGradeCatalog {
        return $this->catalog;
    }

    public function setCatalog(TuitionGradeCatalog $catalog): TuitionGradeCatalogGrade {
        $this->catalog = $catalog;
        return $this;
    }

    public function getValue(): ?string {
        return $this->value;
    }

    public function setValue(?string $value): TuitionGradeCatalogGrade {
        $this->value = $value;
        return $this;
    }

    public function getColor(): ?string {
        return $this->color;
    }

    public function setColor(?string $color): TuitionGradeCatalogGrade {
        $this->color = $color;
        return $this;
    }
}