<?php

namespace App\Tests\Entity;

use App\Entity\TimetablePeriod;
use App\Entity\TimetablePeriodVisibility;
use App\Entity\UserType;
use PHPUnit\Framework\TestCase;

class TimetablePeriodVisibilityTest extends TestCase {
    public function testGettersSetters() {
        $visibility = new TimetablePeriodVisibility();

        $period = new TimetablePeriod();
        $visibility->setPeriod($period);
        $this->assertEquals($period, $visibility->getPeriod());

        $type = UserType::Student();
        $visibility->setUserType($type);
        $this->assertEquals($type, $visibility->getUserType());
    }
}