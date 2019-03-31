<?php

namespace App\Tests\Request;

use App\Request\BadRequestException;
use App\Response\ErrorResponse;
use PHPUnit\Framework\TestCase;

class BadRequestExceptionTest extends TestCase {
    public function testConstructor() {
        $response = new ErrorResponse('Test response');
        $exception = new BadRequestException($response);

        $this->assertEquals($response, $exception->getResponse());
    }
}