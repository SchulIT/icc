<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;

class ErrorResponse {

    /**
     * @Serializer\Type("string")
     * @var string
     */
    private $message;

    /**
     * @Serializer\Type("array<string>")
     * @SWG\Property(description="This property may or may not contain information about the error.")
     * @var mixed[]
     */
    private $data = [ ];

    public function __construct(string $message) {
        $this->message = $message;
    }

    public function getMessage(): string {
        return $this->message;
    }

    public function getData() {
        return $this->data;
    }

    public function setData(array $data) {
        $this->data = $data;
    }
}