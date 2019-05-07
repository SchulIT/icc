<?php

namespace App\Tests\Grouping;

use App\Grouping\Grouper;
use App\Sorting\AppointmentDateGroupStrategy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GrouperTest extends TestCase {

    /**
     * @expectedException Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function testServiceNotExistent() {
        $grouper = new Grouper([new AppointmentDateGroupStrategy()]);
        $grouper->group([], 'App\Tests\Grouping\NonExistingService');
    }
}