<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait SectionAwareTrait {

    /**
     * @ORM\ManyToOne(targetEntity="Section")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Section|null
     */
    #[Assert\NotNull]
    private $section;

    public function getSection(): ?Section {
        return $this->section;
    }

    public function setSection(?Section $section): self {
        $this->section = $section;
        return $this;
    }
}