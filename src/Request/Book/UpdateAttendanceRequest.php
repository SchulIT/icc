<?php

namespace App\Request\Book;

use App\Validator\CsrfToken;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateAttendanceRequest {
    /**
     * @Serializer\SerializedName("_token")
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @CsrfToken(id="update_attendance")
     * @var string|null
     */
    private ?string $csrfToken;

    /**
     * @Serializer\SerializedName("type")
     * @Serializer\Type("int")
     * @Assert\Choice(choices={0, 1, 2 })
     * @var int
     */
    private int $type;

    /**
     * @Serializer\SerializedName("absent_lessons")
     * @Serializer\Type("int")
     * @var int
     */
    private int $absentLessons;

    /**
     * @Serializer\SerializedName("late_minutes")
     * @Serializer\Type("int")
     * @var int
     */
    private int $lateMinutes;

    /**
     * @Serializer\SerializedName("excuse_status")
     * @Serializer\Type("int")
     * @var int
     */
    private int $excuseStatus;

    /**
     * @Serializer\SerializedName("comment")
     * @Serializer\Type("string")
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private ?string $comment;

    /**
     * @return string|null
     */
    public function getCsrfToken(): ?string {
        return $this->csrfToken;
    }

    /**
     * @param string|null $csrfToken
     * @return UpdateAttendanceRequest
     */
    public function setCsrfToken(?string $csrfToken): UpdateAttendanceRequest {
        $this->csrfToken = $csrfToken;
        return $this;
    }

    /**
     * @return int
     */
    public function getType(): int {
        return $this->type;
    }

    /**
     * @param int $type
     * @return UpdateAttendanceRequest
     */
    public function setType(int $type): UpdateAttendanceRequest {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getAbsentLessons(): int {
        return $this->absentLessons;
    }

    /**
     * @param int $absentLessons
     * @return UpdateAttendanceRequest
     */
    public function setAbsentLessons(int $absentLessons): UpdateAttendanceRequest {
        $this->absentLessons = $absentLessons;
        return $this;
    }

    /**
     * @return int
     */
    public function getLateMinutes(): int {
        return $this->lateMinutes;
    }

    /**
     * @param int $lateMinutes
     * @return UpdateAttendanceRequest
     */
    public function setLateMinutes(int $lateMinutes): UpdateAttendanceRequest {
        $this->lateMinutes = $lateMinutes;
        return $this;
    }

    /**
     * @return int
     */
    public function getExcuseStatus(): int {
        return $this->excuseStatus;
    }

    /**
     * @param int $excuseStatus
     * @return UpdateAttendanceRequest
     */
    public function setExcuseStatus(int $excuseStatus): UpdateAttendanceRequest {
        $this->excuseStatus = $excuseStatus;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     * @return UpdateAttendanceRequest
     */
    public function setComment(?string $comment): UpdateAttendanceRequest {
        $this->comment = $comment;
        return $this;
    }
}