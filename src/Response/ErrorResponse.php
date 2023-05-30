<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

/**
 * Indicates a non-successful import.
 */
class ErrorResponse {

    public function __construct(
        #[Serializer\SerializedName('message')]
        #[Serializer\Type('string')]
        private string $message,
        /**
         * Type of exception (optional).
         */
        #[Serializer\Type('string')]
        #[Serializer\SerializedName('type')]
        private ?string $type = null
    )
    {
    }

    public function getType(): ?string {
        return $this->type;
    }

    public function getMessage(): string {
        return $this->message;
    }

}