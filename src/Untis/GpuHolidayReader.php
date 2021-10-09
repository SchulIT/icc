<?php

namespace App\Untis;

use League\Csv\Reader;

class GpuHolidayReader extends AbstractGpuReader {

    /**
     * @param Reader $reader
     * @return GpuHoliday[]
     */
    public function readGpu(Reader $reader): array {
        $this->prepareReader($reader);
        $holidays = [ ];

        foreach($reader->getRecords() as $record) {
            $holidays[] = (new GpuHoliday())
                ->setShortName($record[0])
                ->setLongName($record[1])
                ->setFrom($this->convertDate($record[2]))
                ->setTo($this->convertDate($record[3]))
                ->setIsHoliday($record[4] === 'F');
        }

        return $holidays;
    }
}