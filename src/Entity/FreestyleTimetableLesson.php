<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class FreestyleTimetableLesson extends TimetableLesson {

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $subject;

    /**
     * @return string|null
     */
    public function getSubject(): ?string {
        return $this->subject;
    }

    /**
     * @param string|null $subject
     * @return TimetableLesson
     */
    public function setSubject(?string $subject): TimetableLesson {
        $this->subject = $subject;
        return $this;
    }
}