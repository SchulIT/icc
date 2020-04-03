<?php

namespace App\Tests\Entity;

use App\Entity\TimetablePeriod;
use App\Entity\UserTypeEntity;
use PHPUnit\Framework\TestCase;

class TimetablePeriodTest extends TestCase {
    public function testGettersSetters() {
        $period = new TimetablePeriod();

        $period->setExternalId('external-id');
        $this->assertEquals('external-id', $period->getExternalId());

        $period->setName('name');
        $this->assertEquals('name', $period->getName());

        $start = new \DateTime('2019-01-01');
        $period->setStart($start);
        $this->assertEquals($start, $period->getStart());

        $end = new \DateTime('2019-03-01');
        $period->setEnd($end);
        $this->assertEquals($end, $period->getEnd());

        $visibility = new UserTypeEntity();
        $period->addVisibility($visibility);
        $this->assertTrue($period->getVisibilities()->contains($visibility));

        $period->removeVisibility($visibility);
        $this->assertFalse($period->getVisibilities()->contains($visibility));
    }
}