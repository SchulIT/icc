<?php

namespace App\Tests\Untis;

use App\Untis\SupervisionReader;
use League\Csv\Reader;
use PHPUnit\Framework\TestCase;

/**
 * Test cases from https://github.com/stuebersystems/enbrea.untis.gpu/blob/main/test/Xunit/TestGpuSupervision.cs
 * Big thanks to Stueber Systems
 */
class GpuSupervisionReaderTest extends TestCase {
    public function testFilledLine() {
        $line = <<<GPU
"S1";"HauGe";1;4;20;"24~25~31~32~39~40";
GPU;
        $reader = new SupervisionReader();
        $supervisions = $reader->readGpu(Reader::createFromString($line));

        $this->assertEquals(1, count($supervisions));

        $supervision = $supervisions[0];
        $this->assertEquals('S1', $supervision->getCorridor());
        $this->assertEquals('HauGe', $supervision->getTeacher());
        $this->assertEquals(1, $supervision->getDay());
        $this->assertEquals(4, $supervision->getLesson());
        $this->assertEquals([24,25,31,32,39,40], $supervision->getWeeks());
    }
}