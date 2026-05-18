<?php

namespace App\Untis\Gpu\Room;

use App\Framework\Import\Importer;
use App\Framework\Import\ImportResult;
use App\Common\Import\RoomImportStrategy;
use App\Common\Import\Json\RoomData;
use App\Common\Import\Json\RoomsData;
use League\Csv\Reader;

class RoomImporter {
    public function __construct(private readonly Importer $importer, private readonly RoomImportStrategy $strategy, private readonly RoomReader $reader) {

    }

    public function import(Reader $csvReader): ImportResult {
        $gpuRooms = $this->reader->readGpu($csvReader);
        $data = new RoomsData();
        $rooms = [ ];

        foreach($gpuRooms as $gpuRoom) {
            $rooms[] = (new RoomData())
                ->setId($gpuRoom->getShortName())
                ->setName($gpuRoom->getShortName())
                ->setDescription($gpuRoom->getLongName())
                ->setCapacity($gpuRoom->getCapacity());
        }

        $data->setRooms($rooms);
        return $this->importer->import($data, $this->strategy);
    }
}