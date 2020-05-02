<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

class Violation {
    /**
     * Property on which this violation occurred.
     *
     * @Serializer\SerializedName("property")
     * @Serializer\Type("string")
     * @var string
     */
    private $property;

    /**
     * Violation message.
     *
     * @Serializer\SerializedName("message")
     * @Serializer\Type("string")
     * @var string
     */
    private $message;

    public function __construct(string $property, string $message) {
        $this->property = $property;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getProperty(): string {
        return $this->property;
    }

    /**
     * @return string
     */
    public function getMessage(): string {
        return $this->message;
    }
}