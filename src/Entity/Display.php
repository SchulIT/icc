<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class Display {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $name;

    /**
     * @ORM\Column(type="display_target_user_type")
     * @Assert\NotNull()
     * @var DisplayTargetUserType|null
     */
    private $targetUserType;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(0)
     * @var int
     */
    private $refreshTime = 20;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $showDate = true;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $showTime = true;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $showWeek = true;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $showInfotexts = true;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $showAbsences = true;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $showExams = true;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return string|null
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return Display
     */
    public function setName(?string $name): Display {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getRefreshTime(): int {
        return $this->refreshTime;
    }

    /**
     * @param int $refreshTime
     * @return Display
     */
    public function setRefreshTime(int $refreshTime): Display {
        $this->refreshTime = $refreshTime;
        return $this;
    }

    /**
     * @return bool
     */
    public function isShowDate(): bool {
        return $this->showDate;
    }

    /**
     * @param bool $showDate
     * @return Display
     */
    public function setShowDate(bool $showDate): Display {
        $this->showDate = $showDate;
        return $this;
    }

    /**
     * @return bool
     */
    public function isShowTime(): bool {
        return $this->showTime;
    }

    /**
     * @param bool $showTime
     * @return Display
     */
    public function setShowTime(bool $showTime): Display {
        $this->showTime = $showTime;
        return $this;
    }

    /**
     * @return bool
     */
    public function isShowWeek(): bool {
        return $this->showWeek;
    }

    /**
     * @param bool $showWeek
     * @return Display
     */
    public function setShowWeek(bool $showWeek): Display {
        $this->showWeek = $showWeek;
        return $this;
    }

    /**
     * @return bool
     */
    public function isShowInfotexts(): bool {
        return $this->showInfotexts;
    }

    /**
     * @param bool $showInfotexts
     * @return Display
     */
    public function setShowInfotexts(bool $showInfotexts): Display {
        $this->showInfotexts = $showInfotexts;
        return $this;
    }

    /**
     * @return bool
     */
    public function isShowAbsences(): bool {
        return $this->showAbsences;
    }

    /**
     * @param bool $showAbsences
     * @return Display
     */
    public function setShowAbsences(bool $showAbsences): Display {
        $this->showAbsences = $showAbsences;
        return $this;
    }

    /**
     * @return DisplayTargetUserType|null
     */
    public function getTargetUserType(): ?DisplayTargetUserType {
        return $this->targetUserType;
    }

    /**
     * @param DisplayTargetUserType|null $targetUserType
     * @return Display
     */
    public function setTargetUserType(?DisplayTargetUserType $targetUserType): Display {
        $this->targetUserType = $targetUserType;
        return $this;
    }

    /**
     * @return bool
     */
    public function isShowExams(): bool {
        return $this->showExams;
    }

    /**
     * @param bool $showExams
     * @return Display
     */
    public function setShowExams(bool $showExams): Display {
        $this->showExams = $showExams;
        return $this;
    }

}