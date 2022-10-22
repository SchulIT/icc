<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

class Violation {
    public function __construct(
        /**
         * Property on which this violation occurred.
         *
         * @Serializer\SerializedName("property")
         * @Serializer\Type("string")
         */
        private string $property,
        /**
         * Violation message.
         *
         * @Serializer\SerializedName("message")
         * @Serializer\Type("string")
         */
        private string $message
    )
    {
    }

    public function getProperty(): string {
        return $this->property;
    }

    public function getMessage(): string {
        return $this->message;
    }
}