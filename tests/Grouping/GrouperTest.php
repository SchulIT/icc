<?php

namespace App\Tests\Grouping;

use App\Dashboard\Grouping\AbsentStudentStrategy;
use App\Framework\Grouping\Grouper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class GrouperTest extends TestCase {

    public function testServiceNotExistent() {
        $this->expectException(ServiceNotFoundException::class);
        $grouper = new Grouper([new AbsentStudentStrategy()]);
        $grouper->group([], 'App\Tests\Grouping\NonExistingService');
    }
}
