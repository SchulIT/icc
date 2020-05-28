<?php

namespace App\Tests\Functional\EventSubscriber;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiExceptionSubscriberTest extends WebTestCase {
    private const InvalidJson = <<<JSON
{
    foo: bla
}
JSON;

    private const NonValidJson = <<<JSON
{
    "appointments": [
        {
            "id": "foo"
        }
    ]
}
JSON;

    public function testInvalidJsonReturnsBadRequestJsonResponse() {
        $client = static::createClient();
        $client->request('POST', '/api/import/appointments', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_TOKEN' => getenv('IMPORT_PSK')
        ], static::InvalidJson);

        $response = $client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $jsonResponse = json_decode($response->getContent());

        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        $this->assertObjectHasAttribute('message', $jsonResponse);
    }

    public function testInvalidJsonReturnsConstraintViolationJsonResponse() {
        $client = static::createClient();

        $client->request('POST', '/api/import/appointments', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_TOKEN' => getenv('IMPORT_PSK')
        ], static::NonValidJson);

        $response = $client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $jsonResponse = json_decode($response->getContent());

        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        $this->assertObjectHasAttribute('violations', $jsonResponse);
    }
}