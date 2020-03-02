<?php

namespace App\Tests\Entity;

use App\Entity\Appointment;
use App\Entity\AppointmentCategory;
use App\Entity\StudyGroup;
use App\Entity\Teacher;
use PHPUnit\Framework\TestCase;

class AppointmentTest extends TestCase {
    public function testGettersSetters() {
        $appointment = new Appointment();

        $this->assertNull($appointment->getId());

        $appointment->setExternalId('external-id');
        $this->assertEquals('external-id', $appointment->getExternalId());

        $appointment->setTitle('subject');
        $this->assertEquals('subject', $appointment->getTitle());

        $appointment->setContent('content');
        $this->assertEquals('content', $appointment->getContent());

        $appointment->setContent(null);
        $this->assertNull($appointment->getContent());

        $start = new \DateTime('2019-01-01');
        $appointment->setStart($start);
        $this->assertEquals($start, $appointment->getStart());

        $end = new \DateTime('2019-02-01');
        $appointment->setEnd($end);
        $this->assertEquals($end, $appointment->getEnd());

        $appointment->setLocation('location');
        $this->assertEquals('location', $appointment->getLocation());

        $appointment->setLocation(null);
        $this->assertNull($appointment->getLocation());

        $appointment->setAllDay(true);
        $this->assertTrue($appointment->isAllDay());

        $appointment->setAllDay(false);
        $this->assertFalse($appointment->isAllDay());

        $category = new AppointmentCategory();
        $appointment->setCategory($category);
        $this->assertEquals($category, $appointment->getCategory());

        $appointment->setExternalOrganizers('external organizers');
        $this->assertEquals('external organizers', $appointment->getExternalOrganizers());

        $appointment->setExternalOrganizers(null);
        $this->assertNull($appointment->getExternalOrganizers());

        $teacher = new Teacher();
        $appointment->addOrganizer($teacher);
        $this->assertTrue($appointment->getOrganizers()->contains($teacher));

        $appointment->removeOrganizer($teacher);
        $this->assertFalse($appointment->getOrganizers()->contains($teacher));

        $studyGroup = new StudyGroup();
        $appointment->addStudyGroup($studyGroup);
        $this->assertTrue($appointment->getStudyGroups()->contains($studyGroup));

        $appointment->removeStudyGroup($studyGroup);
        $this->assertFalse($appointment->getStudyGroups()->contains($studyGroup));
    }
}