<?php

namespace App\Tests\Converter;

use App\Entity\Grade;
use App\Converter\GradesCollapsedArrayConverter;
use PHPUnit\Framework\TestCase;

class GradeUtilsTest extends TestCase {
    public function testCollapseWithPreceedingZeros() {
        $converter = new GradesCollapsedArrayConverter();

        $grades = [
            (new Grade())->setName('05A'),
            (new Grade())->setName('05B'),
            (new Grade())->setName('06B'),
            (new Grade())->setName('06C'),
            (new Grade())->setName('EF')
        ];

        $collapsed = $converter->convert($grades);
        $this->assertEquals(['05AB', '06BC', 'EF'], $collapsed);
    }

    public function testCollapseWithoutPreceedingZeros() {
        $converter = new GradesCollapsedArrayConverter();

        $grades = [
            (new Grade())->setName('5A'),
            (new Grade())->setName('5B'),
            (new Grade())->setName('6B'),
            (new Grade())->setName('6C'),
            (new Grade())->setName('EF')
        ];

        $collapsed = $converter->convert($grades);
        $this->assertEquals(['5AB', '6BC', 'EF'], $collapsed);
    }
}