<?php

namespace App\Tests\Grouping;

use App\Entity\Appointment;
use App\Grouping\AppointmentDateStrategy;
use App\Grouping\Grouper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AppointmentDateGroupStrategyTest extends TestCase {
    private function getTestData() {
        $dates = [
            '2018-12-24',
            '2018-12-25',
            '2018-12-24',
            '2018-12-31',
            '2019-01-01',
            '2019-01-01',
            '2019-01-02'
        ];

        $appointments = [ ];

        foreach($dates as $startDate) {
            $appointments[] = (new Appointment())
                ->setStart(new \DateTime($startDate));
        }

        return $appointments;
    }

    public function testGrouping() {
        $strategy = new AppointmentDateStrategy();

        $containerMock = $this->createMock(ContainerInterface::class);
        $containerMock
            ->method('get')
            ->with(AppointmentDateStrategy::class)
            ->willReturn($strategy);

        $grouper = new Grouper();
        $grouper->setContainer($containerMock);

        $array = $this->getTestData();
        $groups = $grouper->group($array, AppointmentDateStrategy::class);

        $this->assertEquals(5, count($groups));
    }
}