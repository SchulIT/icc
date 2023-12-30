<?php

namespace App\Entity;

use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[ORM\Entity]
class Display {

    use IdTrait;
    use UuidTrait;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string')]
    private ?string $name = null;

    #[Assert\NotNull]
    #[ORM\Column(type: 'string', enumType: DisplayTargetUserType::class)]
    private ?DisplayTargetUserType $targetUserType = null;

    #[Assert\GreaterThan(0)]
    #[ORM\Column(type: 'integer')]
    private int $refreshTime = 600;

    #[Assert\GreaterThan(0)]
    #[ORM\Column(type: 'integer')]
    private int $scrollTime = 20;

    #[Assert\GreaterThan(0)]
    #[ORM\Column(type: 'integer')]
    private int $numberOfColumns = 2;

    #[ORM\Column(type: 'boolean')]
    private bool $showDate = true;

    #[ORM\Column(type: 'boolean')]
    private bool $showTime = true;

    #[ORM\Column(type: 'boolean')]
    private bool $showWeek = true;

    #[ORM\Column(type: 'boolean')]
    private bool $showInfotexts = true;

    #[ORM\Column(type: 'boolean')]
    private bool $showAbsences = true;

    #[ORM\Column(type: 'boolean')]
    private bool $showExams = true;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $countdownDate = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $countdownText = null;

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

    public function getNumberOfColumns(): int {
        return $this->numberOfColumns;
    }

    public function setNumberOfColumns(int $numberOfColumns): Display {
        $this->numberOfColumns = $numberOfColumns;
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

    /**
     * @return DateTime|null
     */
    public function getCountdownDate(): ?DateTime {
        return $this->countdownDate;
    }

    /**
     * @param DateTime|null $countdownDate
     * @return Display
     */
    public function setCountdownDate(?DateTime $countdownDate): Display {
        $this->countdownDate = $countdownDate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCountdownText(): ?string {
        return $this->countdownText;
    }

    /**
     * @param string|null $countdownText
     * @return Display
     */
    public function setCountdownText(?string $countdownText): Display {
        $this->countdownText = $countdownText;
        return $this;
    }
}