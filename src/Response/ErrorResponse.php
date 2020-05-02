<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

/**
 * Indicates a non-successful import.
 */
class ErrorResponse {

    /**
     * Type of exception (optional).
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("type")
     * @var string
     */
    private $type;

    /**
     * @Serializer\SerializedName("message")
     * @Serializer\Type("string")
     * @var string
     */
    private $message;

    public function __construct(string $message, ?string $type = null) {
        $this->message = $message;
        $this->type = $type;
    }

    public function getType(): ?string {
        return $this->type;
    }

    public function getMessage(): string {
        return $this->message;
    }

}