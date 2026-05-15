<?php

namespace App\Tests\Grouping;

use App\Framework\Grouping\Grouper;
use App\Appointment\Sorting\AppointmentDateGroupStrategy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class GrouperTest extends TestCase {

    public function testServiceNotExistent() {
        $this->expectException(ServiceNotFoundException::class);
        $grouper = new Grouper([new AppointmentDateGroupStrategy()]);
        $grouper->group([], 'App\Tests\Grouping\NonExistingService');
    }
}