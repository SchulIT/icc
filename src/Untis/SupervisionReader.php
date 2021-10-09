<?php

namespace App\Untis;

use League\Csv\Reader;

class SupervisionReader extends AbstractGpuReader {

    /**
     * @param Reader $reader
     * @return GpuSupervision[]
     */
    public function readGpu(Reader $reader): array {
        $this->prepareReader($reader);
        $supervisions = [ ];

        foreach($reader->getRecords() as $record) {
            if(count($record) < 6) {
                continue;
            }

            $supervision = new GpuSupervision();
            $supervision->setCorridor($record[0]);
            $supervision->setTeacher($record[1]);
            $supervision->setDay($this->getInt($record[2]));
            $supervision->setLesson($this->getInt($record[3]));
            $supervision->setWeeks($this->getIntArrayOrEmptyArray($record[5]));

            $supervisions[] = $supervision;
        }

        return $supervisions;
    }
}