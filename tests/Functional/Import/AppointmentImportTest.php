<?php

namespace App\Tests\Functional\Import;

use App\Entity\Appointment;
use App\Entity\AppointmentCategory;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AppointmentImportTest extends WebTestCase {

    private const SimpleAppointmentJson = <<<JSON
{
    "appointments": [
        {
            "id": "my-id",
            "subject": "Fancy appointment",
            "content": "My content",
            "location": "On Earth",
            "start": "2020-05-28T10:00:00",
            "end": "2020-05-28T12:00:00",
            "visibilities": [ "student" ],
            "category": "category-id",
            "is_all_day": false,
            "study_groups": [ ],
            "organizers": [ ]
        }
    ]
}
JSON;

    public function testImportSimpleAppointment() {
        $client = static::createClient();

        /** @var ObjectManager $doctrine */
        $doctrine = $client->getContainer()->get('doctrine')->getManager();

        $category = (new AppointmentCategory())
            ->setName('name')
            ->setColor('#000000')
            ->setExternalId('category-id');

        $doctrine->persist($category);
        $doctrine->flush();

        $client->request('POST', '/api/import/appointments', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_TOKEN' => getenv('IMPORT_PSK')
        ], static::SimpleAppointmentJson);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());


        /** @var Appointment|null $appointment */
        $appointment = $doctrine->getRepository(Appointment::class)
            ->findOneBy([
                'externalId' => 'my-id'
            ]);

        $doctrine->clear();

        $this->assertNotNull($appointment);
        $this->assertEquals(new DateTime('2020-05-28T10:00:00'), $appointment->getStart());
        $this->assertEquals(new DateTime('2020-05-28T12:00:00'), $appointment->getEnd());
    }
}