<?php

namespace App\Tests\Grouping;

use App\Grouping\Grouper;
use App\Sorting\AppointmentDateGroupStrategy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class GrouperTest extends TestCase {

    public function testServiceNotExistent() {
        $this->expectException(ServiceNotFoundException::class);
        $grouper = new Grouper([new AppointmentDateGroupStrategy()]);
        $grouper->group([], 'App\Tests\Grouping\NonExistingService');
    }
}