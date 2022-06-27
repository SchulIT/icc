<?php

namespace App\Untis\Database\Date;

use App\Untis\Database\AbstractDatabaseReader;
use App\Untis\Database\Date\Date;
use League\Csv\Reader;

class DateReader extends AbstractDatabaseReader {

    /**
     * @param Reader $reader
     * @return Date[]
     */
    public function readDatabase(Reader $reader): array {
        $this->prepareReader($reader);
        $dates = [ ];

        foreach($reader->getRecords() as $record) {
            $date = new Date();
            $date->setCalendarWeek($this->getInt($record[0]));
            $date->setStartDate($this->convertDate($record[2]));
            $date->setSchoolWeek($this->getInt($record[3]));

            $dates[] = $date;
        }

        return $dates;
    }
}