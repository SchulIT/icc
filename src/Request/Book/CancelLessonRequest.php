<?php

namespace App\Request\Book;

use App\Validator\CsrfToken;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class CancelLessonRequest {

    #[CsrfToken(id: 'cancel_lesson')]
    #[Assert\NotBlank]
    #[Serializer\SerializedName('_token')]
    #[Serializer\Type('string')]
    private ?string $csrfToken = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Serializer\SerializedName('reason')]
    #[Serializer\Type('string')]
    private ?string $reason = null;

    public function getCsrfToken(): ?string {
        return $this->csrfToken;
    }

    public function setCsrfToken(?string $csrfToken): CancelLessonRequest {
        $this->csrfToken = $csrfToken;
        return $this;
    }

    public function getReason(): ?string {
        return $this->reason;
    }

    public function setReason(?string $reason): CancelLessonRequest {
        $this->reason = $reason;
        return $this;
    }
}