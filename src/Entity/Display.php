<?php

namespace App\Entity;

use App\Validator\Color;
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
    private $substitutionsTarget;

    /**
     * @ORM\Column(type="text", length=7, nullable=true)
     * @Assert\NotBlank(allowNull=true)
     * @Color()
     * @var string|null
     */
    private $backgroundColor = null;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $maxNumberOfRows = 20;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private $fontFamily = null;

    /**
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
     * @ORM\Column(type="display_target_user_type", nullable=true)
     * @var DisplayTargetUserType|null
     */
    private $appointmentsTarget = null;

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
     * @return DisplayTargetUserType|null
     */
    public function getSubstitutionsTarget(): ?DisplayTargetUserType {
        return $this->substitutionsTarget;
    }

    /**
     * @param DisplayTargetUserType|null $substitutionsTarget
     * @return Display
     */
    public function setSubstitutionsTarget(?DisplayTargetUserType $substitutionsTarget): Display {
        $this->substitutionsTarget = $substitutionsTarget;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBackgroundColor(): ?string {
        return $this->backgroundColor;
    }

    /**
     * @param string|null $backgroundColor
     * @return Display
     */
    public function setBackgroundColor(?string $backgroundColor): Display {
        $this->backgroundColor = $backgroundColor;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxNumberOfRows(): int {
        return $this->maxNumberOfRows;
    }

    /**
     * @param int $maxNumberOfRows
     * @return Display
     */
    public function setMaxNumberOfRows(int $maxNumberOfRows): Display {
        $this->maxNumberOfRows = $maxNumberOfRows;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFontFamily(): ?string {
        return $this->fontFamily;
    }

    /**
     * @param string|null $fontFamily
     * @return Display
     */
    public function setFontFamily(?string $fontFamily): Display {
        $this->fontFamily = $fontFamily;
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
    public function getAppointmentsTarget(): ?DisplayTargetUserType {
        return $this->appointmentsTarget;
    }

    /**
     * @param DisplayTargetUserType|null $appointmentsTarget
     * @return Display
     */
    public function setAppointmentsTarget(?DisplayTargetUserType $appointmentsTarget): Display {
        $this->appointmentsTarget = $appointmentsTarget;
        return $this;
    }
}