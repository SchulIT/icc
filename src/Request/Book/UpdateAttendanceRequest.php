<?php

namespace App\Request\Book;

use App\Validator\CsrfToken;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateAttendanceRequest {
    /**
     * @var string|null
     */
    #[CsrfToken(id: 'update_attendance')]
    #[Assert\NotBlank]
    #[Serializer\SerializedName('_token')]
    #[Serializer\Type('string')]
    private ?string $csrfToken = null;

    /**
     * @var int
     */
    #[Assert\Choice(choices: [0, 1, 2])]
    #[Serializer\SerializedName('type')]
    #[Serializer\Type('int')]
    private int $type;

    /**
     * @var int
     */
    #[Serializer\SerializedName('absent_lessons')]
    #[Serializer\Type('int')]
    private int $absentLessons;

    /**
     * @var int
     */
    #[Serializer\SerializedName('late_minutes')]
    #[Serializer\Type('int')]
    private int $lateMinutes;

    /**
     * @var int
     */
    #[Serializer\SerializedName('excuse_status')]
    #[Serializer\Type('int')]
    private int $excuseStatus;

    /**
     * @var string|null
     */
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    #[Serializer\SerializedName('comment')]
    #[Serializer\Type('string')]
    private ?string $comment = null;

    public function getCsrfToken(): ?string {
        return $this->csrfToken;
    }

    public function setCsrfToken(?string $csrfToken): UpdateAttendanceRequest {
        $this->csrfToken = $csrfToken;
        return $this;
    }

    public function getType(): int {
        return $this->type;
    }

    public function setType(int $type): UpdateAttendanceRequest {
        $this->type = $type;
        return $this;
    }

    public function getAbsentLessons(): int {
        return $this->absentLessons;
    }

    public function setAbsentLessons(int $absentLessons): UpdateAttendanceRequest {
        $this->absentLessons = $absentLessons;
        return $this;
    }

    public function getLateMinutes(): int {
        return $this->lateMinutes;
    }

    public function setLateMinutes(int $lateMinutes): UpdateAttendanceRequest {
        $this->lateMinutes = $lateMinutes;
        return $this;
    }

    public function getExcuseStatus(): int {
        return $this->excuseStatus;
    }

    public function setExcuseStatus(int $excuseStatus): UpdateAttendanceRequest {
        $this->excuseStatus = $excuseStatus;
        return $this;
    }

    public function getComment(): ?string {
        return $this->comment;
    }

    public function setComment(?string $comment): UpdateAttendanceRequest {
        $this->comment = $comment;
        return $this;
    }
}