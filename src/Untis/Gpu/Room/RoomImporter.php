<?php

namespace App\Untis\Gpu\Room;

use App\Import\Importer;
use App\Import\ImportResult;
use App\Import\RoomImportStrategy;
use App\Request\Data\RoomData;
use App\Request\Data\RoomsData;
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