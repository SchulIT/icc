<?php

namespace App\Entity;

use App\Validator\PeriodNotOverlaps;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @UniqueEntity(fields={"externalId"})
 * @PeriodNotOverlaps()
 */
class TimetablePeriod {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     * @var string
     */
    private $externalId;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotNull()
     * @Assert\Date()
     * @var \DateTime
     */
    private $start;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotNull()
     * @Assert\Date()
     * @var \DateTime
     */
    private $end;

    /**
     * @ORM\OneToMany(targetEntity="TimetablePeriodVisibility", mappedBy="period", cascade={"persist"})
     * @var ArrayCollection<TimetablePeriodVisibility>
     */
    private $visibilities;

    /**
     * @ORM\OneToMany(targetEntity="TimetableLesson", mappedBy="period")
     * @var ArrayCollection<TimetableLesson>
     */
    private $lessons;

    /**
     * @ORM\OneToMany(targetEntity="TimetableSupervision", mappedBy="period")
     * @var ArrayCollection<TimetableSupervision>
     */
    private $supervisions;

    public function __construct() {
        $this->visibilities = new ArrayCollection();
        $this->lessons = new ArrayCollection();
        $this->supervisions = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     * @return TimetablePeriod
     */
    public function setId(int $id): TimetablePeriod {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getExternalId(): string {
        return $this->externalId;
    }

    /**
     * @param string $externalId
     * @return TimetablePeriod
     */
    public function setExternalId(string $externalId): TimetablePeriod {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return TimetablePeriod
     */
    public function setName(string $name): TimetablePeriod {
        $this->name = $name;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStart(): \DateTime {
        return $this->start;
    }

    /**
     * @param \DateTime $start
     * @return TimetablePeriod
     */
    public function setStart(\DateTime $start): TimetablePeriod {
        $this->start = $start;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEnd(): \DateTime {
        return $this->end;
    }

    /**
     * @param \DateTime $end
     * @return TimetablePeriod
     */
    public function setEnd(\DateTime $end): TimetablePeriod {
        $this->end = $end;
        return $this;
    }

    public function addVisibility(TimetablePeriodVisibility $visibility) {
        $this->visibilities->add($visibility);
    }

    public function removeVisibility(TimetablePeriodVisibility $visibility) {
        $this->visibilities->removeElement($visibility);
    }

    /**
     * @return ArrayCollection<TimetablePeriodVisibility>
     */
    public function getVisibilities(): ArrayCollection {
        return $this->visibilities;
    }

    /**
     * @return ArrayCollection<TimetableLesson>
     */
    public function getLessons(): ArrayCollection {
        return $this->lessons;
    }

    /**
     * @return ArrayCollection<TimetableSupervision>
     */
    public function getSupervisions(): ArrayCollection {
        return $this->supervisions;
    }
}