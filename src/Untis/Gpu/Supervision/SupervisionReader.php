<?php

namespace App\Untis\Gpu\Supervision;

use App\Untis\Gpu\AbstractGpuReader;
use App\Untis\Gpu\Supervision\Supervision;
use League\Csv\Reader;

class SupervisionReader extends AbstractGpuReader {

    /**
     * @return Supervision[]
     */
    public function readGpu(Reader $reader): array {
        $this->prepareReader($reader);
        $supervisions = [ ];

        foreach($reader->getRecords() as $record) {
            $supervision = new Supervision();
            $supervision->setCorridor($record[0]);
            $supervision->setTeacher($record[1]);
            $supervision->setDay($this->getInt($record[2]));
            $supervision->setLesson($this->getInt($record[3]));
            $supervision->setWeeks($this->getIntArrayOrEmptyArray($record[5]));

            if(empty($supervision->getTeacher()) || empty($supervision->getCorridor())) {
                continue;
            }
            
            $supervisions[] = $supervision;
        }

        return $supervisions;
    }
}