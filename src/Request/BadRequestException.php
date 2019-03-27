<?php

namespace App\Request;

use App\Response\ErrorResponse;
use Throwable;

class BadRequestException extends \Exception {
    private $response;

    /**
     * @param string|ErrorResponse $response
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($response, int $code = 400, Throwable $previous = null) {
        if(!$response instanceof ErrorResponse) {
            $response = new ErrorResponse((string)$response);
        }

        parent::__construct($response->getMessage(), $code, $previous);

        $this->response = $response;
    }

    public function getResponse(): ErrorResponse {
        return $this->response;
    }
}