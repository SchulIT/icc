<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 */
class Display {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string")
     */
    #[Assert\NotBlank]
    private ?string $name = null;

    /**
     * @ORM\Column(type="display_target_user_type")
     */
    #[Assert\NotNull]
    private ?DisplayTargetUserType $targetUserType = null;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\GreaterThan(0)]
    private int $refreshTime = 600;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\GreaterThan(0)]
    private int $scrollTime = 20;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $showDate = true;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $showTime = true;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $showWeek = true;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $showInfotexts = true;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $showAbsences = true;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $showExams = true;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): Display {
        $this->name = $name;
        return $this;
    }

    public function getRefreshTime(): int {
        return $this->refreshTime;
    }

    public function setRefreshTime(int $refreshTime): Display {
        $this->refreshTime = $refreshTime;
        return $this;
    }

    public function getScrollTime(): int {
        return $this->scrollTime;
    }

    public function setScrollTime(int $scrollTime): Display {
        $this->scrollTime = $scrollTime;
        return $this;
    }

    public function isShowDate(): bool {
        return $this->showDate;
    }

    public function setShowDate(bool $showDate): Display {
        $this->showDate = $showDate;
        return $this;
    }

    public function isShowTime(): bool {
        return $this->showTime;
    }

    public function setShowTime(bool $showTime): Display {
        $this->showTime = $showTime;
        return $this;
    }

    public function isShowWeek(): bool {
        return $this->showWeek;
    }

    public function setShowWeek(bool $showWeek): Display {
        $this->showWeek = $showWeek;
        return $this;
    }

    public function isShowInfotexts(): bool {
        return $this->showInfotexts;
    }

    public function setShowInfotexts(bool $showInfotexts): Display {
        $this->showInfotexts = $showInfotexts;
        return $this;
    }

    public function isShowAbsences(): bool {
        return $this->showAbsences;
    }

    public function setShowAbsences(bool $showAbsences): Display {
        $this->showAbsences = $showAbsences;
        return $this;
    }

    public function getTargetUserType(): ?DisplayTargetUserType {
        return $this->targetUserType;
    }

    public function setTargetUserType(?DisplayTargetUserType $targetUserType): Display {
        $this->targetUserType = $targetUserType;
        return $this;
    }

    public function isShowExams(): bool {
        return $this->showExams;
    }

    public function setShowExams(bool $showExams): Display {
        $this->showExams = $showExams;
        return $this;
    }

}