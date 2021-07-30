<?php

namespace App\Request\Book;

use App\Validator\CsrfToken;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class CancelLessonRequest {

    /**
     * @Serializer\SerializedName("_token")
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @CsrfToken(id="cancel_lesson")
     * @var string|null
     */
    private $csrfToken;

    /**
     * @Serializer\SerializedName("reason")
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $reason;

    /**
     * @return string|null
     */
    public function getCsrfToken(): ?string {
        return $this->csrfToken;
    }

    /**
     * @param string|null $csrfToken
     * @return CancelLessonRequest
     */
    public function setCsrfToken(?string $csrfToken): CancelLessonRequest {
        $this->csrfToken = $csrfToken;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getReason(): ?string {
        return $this->reason;
    }

    /**
     * @param string|null $reason
     * @return CancelLessonRequest
     */
    public function setReason(?string $reason): CancelLessonRequest {
        $this->reason = $reason;
        return $this;
    }
}