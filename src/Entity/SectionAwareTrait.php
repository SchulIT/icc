<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait SectionAwareTrait {

    /**
     * @ORM\ManyToOne(targetEntity="Section")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull()
     * @var Section|null
     */
    private $section;

    /**
     * @return Section|null
     */
    public function getSection(): ?Section {
        return $this->section;
    }

    /**
     * @param Section|null $section
     */
    public function setSection(?Section $section): self {
        $this->section = $section;
        return $this;
    }
}