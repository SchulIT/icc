<?php

namespace App\Common\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait SectionAwareTrait {

    /**
     * @var Section|null
     */
    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Section::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Section $section = null;

    public function getSection(): ?Section {
        return $this->section;
    }

    public function setSection(?Section $section): self {
        $this->section = $section;
        return $this;
    }
}