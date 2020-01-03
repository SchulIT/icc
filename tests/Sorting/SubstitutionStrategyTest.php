<?php

namespace App\Tests\Sorting;

use App\Entity\Substitution;
use App\Sorting\Sorter;
use App\Sorting\SubstitutionStrategy;
use PHPUnit\Framework\TestCase;

class SubstitutionStrategyTest extends TestCase {

    public function testStrategyWithBeforeItems() {
        $substitutionFirst = (new Substitution())->setStartsBefore(true)->setLessonStart(1)->setLessonEnd(1);
        $substitutionSecond = (new Substitution())->setStartsBefore(false)->setLessonStart(1)->setLessonEnd(2);
        $substitutionThird = (new Substitution())->setStartsBefore(true)->setLessonStart(3)->setLessonEnd(3);

        $list = [
            $substitutionSecond,
            $substitutionFirst,
            $substitutionThird
        ];

        $strategy = new Sorter([new SubstitutionStrategy()]);
        $strategy->sort($list, SubstitutionStrategy::class);

        $this->assertEquals($substitutionFirst, $list[0]);
        $this->assertEquals($substitutionSecond, $list[1]);
        $this->assertEquals($substitutionThird, $list[2]);
    }
}