<?php

namespace App\SickNote;

use App\Entity\DateLesson;
use App\Entity\Student;
use App\Validator\DateLessonGreaterThan;
use App\Validator\DateLessonNotInPast;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class SickNote {
    /**
     * @Assert\NotNull()
     * @var Student|null
     */
    private $student = null;

    /**
     * @Assert\NotNull()
     * @var SickNoteReason|null
     */
    private $reason = null;

    /**
     * @Assert\NotBlank(groups={"quarantine"})
     * @var string|null
     */
    private $orderedBy = null;

    /**
     * @DateLessonNotInPast()
     * @var DateLesson
     */
    private $from;

    /**
     * @param DateLesson $from
     * @return SickNote
     */
    public function setFrom(DateLesson $from): SickNote {
        $this->from = $from;
        return $this;
    }

    /**
     * @param DateLesson $until
     * @return SickNote
     */
    public function setUntil(DateLesson $until): SickNote {
        $this->until = $until;
        return $this;
    }

    /**
     * @DateLessonGreaterThan(propertyPath="from")
     * @var DateLesson
     */
    private $until;

    /**
     * @Assert\NotBlank()
     * @var string|null
     */
    private $message = null;

    /**
     * @var UploadedFile[]
     * @Assert\Count(max="3")
     * @Assert\All(
     *     @Assert\File(maxSize="5M", mimeTypes={"application/pdf", "image/png", "image/jpg", "image/jpeg"})
     * )
     */
    private $attachments = [ ];

    /**
     * @Assert\Email()
     * @var string|null
     */
    private $email = null;

    /**
     * @var string|null
     */
    private $phone = null;

    public function __construct() {
        $this->from = new DateLesson();
        $this->until = new DateLesson();
    }

    /**
     * @return Student|null
     */
    public function getStudent(): ?Student {
        return $this->student;
    }

    /**
     * @param Student|null $student
     * @return SickNote
     */
    public function setStudent(?Student $student): SickNote {
        $this->student = $student;
        return $this;
    }

    /**
     * @return SickNoteReason|null
     */
    public function getReason(): ?SickNoteReason {
        return $this->reason;
    }

    /**
     * @param SickNoteReason|null $reason
     * @return SickNote
     */
    public function setReason(?SickNoteReason $reason): SickNote {
        $this->reason = $reason;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOrderedBy(): ?string {
        return $this->orderedBy;
    }

    /**
     * @param string|null $orderedBy
     * @return SickNote
     */
    public function setOrderedBy(?string $orderedBy): SickNote {
        $this->orderedBy = $orderedBy;
        return $this;
    }

    /**
     * @return DateLesson
     */
    public function getFrom(): DateLesson {
        return $this->from;
    }


    /**
     * @return DateLesson
     */
    public function getUntil(): DateLesson {
        return $this->until;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string {
        return $this->message;
    }

    /**
     * @param string|null $message
     * @return SickNote
     */
    public function setMessage(?string $message): SickNote {
        $this->message = $message;
        return $this;
    }

    /**
     * @return UploadedFile[]
     */
    public function getAttachments(): array {
        return $this->attachments;
    }

    /**
     * @param UploadedFile[] $attachments
     * @return SickNote
     */
    public function setAttachments(array $attachments): SickNote {
        $this->attachments = $attachments;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return SickNote
     */
    public function setEmail(?string $email): SickNote {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     * @return SickNote
     */
    public function setPhone(?string $phone): SickNote {
        $this->phone = $phone;
        return $this;
    }
}