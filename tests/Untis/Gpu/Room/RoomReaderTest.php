<?php

namespace App\Tests\Untis\Gpu\Room;

use App\Untis\Gpu\Room\RoomReader;
use League\Csv\Reader;
use PHPUnit\Framework\TestCase;

class RoomReaderTest extends TestCase {
    public function testReadGpu() {
        $gpu = <<<GPU
"R SIa";"Einstiegsraum 5";"R 5a";;;;3;10;;;;;;;;;;
"R 5a";;"R 5b";;;;3;;;;;;;;;;;
GPU;

        $reader = new RoomReader();
        $rooms = $reader->readGpu(Reader::createFromString($gpu));

        $this->assertCount(2, $rooms);

        $firstRoom = $rooms[0];
        $this->assertEquals('R SIa', $firstRoom->getShortName());
        $this->assertEquals('Einstiegsraum 5', $firstRoom->getLongName());
        $this->assertEquals(10, $firstRoom->getCapacity());

        $secondRoom = $rooms[1];
        $this->assertEquals('R 5a', $secondRoom->getShortName());
        $this->assertNull($secondRoom->getLongName());
        $this->assertNull($secondRoom->getCapacity());
    }
}