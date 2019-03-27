<?php

namespace App\Response;

class ErrorResponse {
    /** @var string */
    private $message;

    /**
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