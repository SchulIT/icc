<?php

namespace App\Tests\Grouping;

use App\Entity\Appointment;
use App\Grouping\AppointmentDateGroup;
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
            '2019-01-02',
            '2019-03-02'
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
        /** @var AppointmentDateGroup[] $groups */
        $groups = $grouper->group($array, AppointmentDateStrategy::class);

        $this->assertEquals(3, count($groups));

        $firstGroup = $groups[0];
        $this->assertEquals(2018, $firstGroup->getYear());
        $this->assertEquals(12, $firstGroup->getMonth());
        $this->assertEquals(4, count($firstGroup->getAppointments()));

        $secondGroup = $groups[1];
        $this->assertEquals(2019, $secondGroup->getYear());
        $this->assertEquals(1, $secondGroup->getMonth());
        $this->assertEquals(3, count($secondGroup->getAppointments()));

        $thirdGroup = $groups[2];
        $this->assertEquals(2019, $thirdGroup->getYear());
        $this->assertEquals(3, $thirdGroup->getMonth());
        $this->assertEquals(1, count($thirdGroup->getAppointments()));
    }
}