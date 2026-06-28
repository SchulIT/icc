<?php

namespace App\Book\Entity;

use App\Common\Entity\IdTrait;
use App\Framework\Validator\Color;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class TuitionGradeCatalogGrade {

    use IdTrait;

    #[ORM\ManyToOne(targetEntity: TuitionGradeCatalog::class, inversedBy: 'grades')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private TuitionGradeCatalog $catalog;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private ?string $value = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    #[Color]
    private ?string $color = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $exportValue = null;

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

    public function getExportValue(): ?string {
        return $this->exportValue;
    }

    public function setExportValue(?string $exportValue): TuitionGradeCatalogGrade {
        $this->exportValue = $exportValue;
        return $this;
    }
}
