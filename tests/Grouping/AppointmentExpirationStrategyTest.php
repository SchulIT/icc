<?php

namespace App\Tests\Grouping;

use App\Entity\Appointment;
use App\Grouping\AppointmentDateGroup;
use App\Grouping\AppointmentExpirationGroup;
use App\Grouping\AppointmentExpirationStrategy;
use App\Grouping\Grouper;
use PHPUnit\Framework\TestCase;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AppointmentExpirationStrategyTest extends TestCase {

    private function getTestData() {
        $dates = [
            ['2018-12-24', true],
            ['2018-12-25 09:00', false],
            ['2018-12-24', true],
            ['2018-12-31', true],
            ['2019-01-01', true],
            ['2019-02-01 10:00', false],
            ['2019-02-02', true],
            ['2019-03-02', true]
        ];

        $appointments = [ ];

        foreach($dates as $date) {
            $startDate = $date[0];
            $isAllDay = $date[1];
            $appointments[] = (new Appointment())
                ->setAllDay($isAllDay)
                ->setEnd(new \DateTime($startDate));
        }

        return $appointments;
    }

    public function testNonAllDayAppointmentEndingTodayIsNotExpired() {
        $dateHelper = $this->createMock(DateHelper::class);
        $dateHelper
            ->method('getNow')
            ->willReturn(new \DateTime('2019-02-01 10:00'));

        $strategy = new AppointmentExpirationStrategy($dateHelper);
        $grouper = new Grouper([$strategy]);

        $appointment = (new Appointment())
            ->setAllDay(false)
            ->setEnd(new \DateTime('2019-02-01 09:59'));
        /** @var AppointmentExpirationGroup[] $groups */
        $groups = $grouper->group([$appointment], AppointmentExpirationStrategy::class);

        $this->assertEquals(1, count($groups));
        $group = $groups[0];

        $this->assertTrue($group->isExpired());
    }

    public function testNonAllDayAppointmentEndingTodayIsExpired() {
        $dateHelper = $this->createMock(DateHelper::class);
        $dateHelper
            ->method('getNow')
            ->willReturn(new \DateTime('2019-02-01 10:00'));

        $strategy = new AppointmentExpirationStrategy($dateHelper);
        $grouper = new Grouper([$strategy]);

        $appointment = (new Appointment())
            ->setAllDay(false)
            ->setEnd(new \DateTime('2019-02-01 10:01'));
        /** @var AppointmentExpirationGroup[] $groups */
        $groups = $grouper->group([$appointment], AppointmentExpirationStrategy::class);

        $this->assertEquals(1, count($groups));
        $group = $groups[0];

        $this->assertFalse($group->isExpired());
    }

    public function testAppointmentEndingTodayIsNotExpired() {
        $dateHelper = $this->createMock(DateHelper::class);
        $dateHelper
            ->method('getNow')
            ->willReturn(new \DateTime('2019-01-02 08:00'));

        $dateHelper
            ->method('getToday')
            ->willReturn(new \DateTime('2019-01-02'));

        $strategy = new AppointmentExpirationStrategy($dateHelper);
        $grouper = new Grouper([$strategy]);

        $appointment = (new Appointment())
            ->setAllDay(true)
            ->setEnd(new \DateTime('2019-01-02'));
        /** @var AppointmentExpirationGroup[] $groups */
        $groups = $grouper->group([$appointment], AppointmentExpirationStrategy::class);

        $this->assertEquals(1, count($groups));
        $group = $groups[0];

        $this->assertFalse($group->isExpired());
    }

    public function testGrouping() {
        $dateHelper = $this->createMock(DateHelper::class);
        $dateHelper
            ->method('getNow')
            ->willReturn(new \DateTime('2019-02-01 08:00'));

        $dateHelper
            ->method('getToday')
            ->willReturn(new \DateTime('2019-02-01'));

        $strategy = new AppointmentExpirationStrategy($dateHelper);
        $grouper = new Grouper([$strategy]);

        $array = $this->getTestData();
        /** @var AppointmentExpirationGroup[] $groups */
        $groups = $grouper->group($array, AppointmentExpirationStrategy::class);

        $this->assertEquals(2, count($groups));

        $firstGroup = $groups[0];
        $this->assertTrue($firstGroup->isExpired());
        $this->assertEquals(5, count($firstGroup->getAppointments()));

        $secondGroup = $groups[1];
        $this->assertFalse($secondGroup->isExpired());
        $this->assertEquals(3, count($secondGroup->getAppointments()));
    }
}