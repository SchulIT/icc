<?php

namespace App\Tests\Functional\Import;

use App\Entity\Appointment;
use App\Entity\AppointmentCategory;
use App\Entity\Section;
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
            "organizers": [ ],
            "mark_students_absent": false
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

        $section = (new Section())
            ->setDisplayName('Testabschnitt')
            ->setYear(2019)
            ->setNumber(2)
            ->setStart(new DateTime('2020-02-01'))
            ->setEnd(new DateTime('2020-07-31'));
        $doctrine->persist($section);
        $doctrine->persist($category);
        $doctrine->flush();

        $client->request('POST', '/api/import/appointments', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_TOKEN' => 'TestToken'
        ], static::SimpleAppointmentJson);

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());


        /** @var Appointment|null $appointment */
        $appointment = $doctrine->getRepository(Appointment::class)
            ->findOneBy([
                'externalId' => 'my-id'
            ]);

        $doctrine->clear();

        $this->assertNotNull($appointment);
        $this->assertEquals(new DateTime('2020-05-28T10:00:00'), $appointment->getStart());
        $this->assertEquals(new DateTime('2020-05-28T12:00:00'), $appointment->getEnd());
        $this->assertFalse($appointment->isMarkStudentsAbsent());
    }
}