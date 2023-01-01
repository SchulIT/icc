<?php

namespace App\Untis\Gpu\Room;

use App\Untis\Gpu\AbstractGpuReader;
use League\Csv\Reader;

class RoomReader extends AbstractGpuReader {

    /**
     * @param Reader $reader
     * @return Room[]
     */
    public function readGpu(Reader $reader): array {
        $this->prepareReader($reader);
        $rooms = [ ];

        foreach($reader->getRecords() as $record) {
            $room = new Room();
            $room->setShortName($record[0]);
            $room->setLongName($this->getStringOrNull($record[1]));
            $room->setCapacity($this->getIntOrNull($record[7]));
            $rooms[] = $room;
        }

        return $rooms;
    }
}