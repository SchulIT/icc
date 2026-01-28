<?php

namespace App\Book\Attendance\Export\Schild;

use JMS\Serializer\Annotation as Serializer;

class ErrorResponse {

    #[Serializer\SerializedName('message')]
    #[Serializer\Type('string')]
    public string $message;

    public function __construct(
        string $message,
    ) {
        $this->message = $message;
    }
}