<?php

namespace App\Tests\Grouping;

use App\Grouping\Grouper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GrouperTest extends TestCase {

    /**
     * @expectedException \RuntimeException
     */
    public function testContainerNotSet() {
        $grouper = new Grouper();

        $grouper->group([], 'App\Tests\Grouping\NonExistingService');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testServiceNotExistent() {
        $containerMock = $this->createMock(ContainerInterface::class);

        $containerMock
            ->method('get')
            ->with('App\Tests\Grouping\NonExistingService')
            ->willReturn(null);

        $grouper = new Grouper();
        $grouper->group([], 'App\Tests\Grouping\NonExistingService');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testInvalidService() {
        $service = new \stdClass();

        $containerMock = $this->createMock(ContainerInterface::class);

        $containerMock
            ->method('get')
            ->with('App\Tests\Grouping\NonExistingService')
            ->willReturn($service);

        $grouper = new Grouper();
        $grouper->group([], 'App\Tests\Grouping\NonExistingService');
    }
}