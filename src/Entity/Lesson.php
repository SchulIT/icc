<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity()
 */
class Lesson {
    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="date")
     * @var DateTime
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $lessonStart;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $lessonEnd;

    /**
     * @ORM\ManyToOne(targetEntity="Tuition")
     * @ORM\JoinColumn()
     * @var Tuition
     */
    private $tuition;

    /**
     * @ORM\OneToMany(targetEntity="LessonEntry", mappedBy="lesson")
     * @var Collection<LessonEntry>
     */
    private $entries;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->entries = new ArrayCollection();
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return Lesson
     */
    public function setDate(DateTime $date): Lesson {
        $this->date = $date;
        return $this;
    }

    /**
     * @return int
     */
    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    /**
     * @param int $lessonStart
     * @return Lesson
     */
    public function setLessonStart(int $lessonStart): Lesson {
        $this->lessonStart = $lessonStart;
        return $this;
    }

    /**
     * @return int
     */
    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }

    /**
     * @param int $lessonEnd
     * @return Lesson
     */
    public function setLessonEnd(int $lessonEnd): Lesson {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    /**
     * @return Tuition
     */
    public function getTuition(): Tuition {
        return $this->tuition;
    }

    /**
     * @param Tuition $tuition
     * @return Lesson
     */
    public function setTuition(Tuition $tuition): Lesson {
        $this->tuition = $tuition;
        return $this;
    }

    /**
     * @return Collection<LessonEntry>
     */
    public function getEntries(): Collection {
        return $this->entries;
    }
}