<?php

namespace App\Request\Message;

use App\Validator\CsrfToken;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class RemoveMessageRequest {

    /**
     * @Serializer\SerializedName("_token")
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @CsrfToken(id="remove_message")
     * @var string|null
     */
    private $csrfToken;

    /**
     * @return string|null
     */
    public function getCsrfToken(): ?string {
        return $this->csrfToken;
    }

    /**
     * @param string|null $csrfToken
     * @return RemoveMessageRequest
     */
    public function setCsrfToken(?string $csrfToken): RemoveMessageRequest {
        $this->csrfToken = $csrfToken;
        return $this;
    }
}