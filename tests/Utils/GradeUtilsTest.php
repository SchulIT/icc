<?php

namespace App\Tests\Utils;

use App\Entity\Grade;
use App\Utils\GradeUtils;
use PHPUnit\Framework\TestCase;

class GradeUtilsTest extends TestCase {
    public function testCollapseWithPreceedingZeros() {
        $utils = new GradeUtils();

        $grades = [
            (new Grade())->setName('05A'),
            (new Grade())->setName('05B'),
            (new Grade())->setName('06B'),
            (new Grade())->setName('06C'),
            (new Grade())->setName('EF')
        ];

        $collapsed = $utils->collapseGradeNames($grades);
        $this->assertEquals(['05AB', '06BC', 'EF'], $collapsed);
    }

    public function testCollapseWithoutPreceedingZeros() {
        $utils = new GradeUtils();

        $grades = [
            (new Grade())->setName('5A'),
            (new Grade())->setName('5B'),
            (new Grade())->setName('6B'),
            (new Grade())->setName('6C'),
            (new Grade())->setName('EF')
        ];

        $collapsed = $utils->collapseGradeNames($grades);
        $this->assertEquals(['5AB', '6BC', 'EF'], $collapsed);
    }
}