<?php

namespace App\Untis;

use League\Csv\Reader;

class DatabaseDateReader extends AbstractDatabaseReader {

    /**
     * @param Reader $reader
     * @return DatabaseDate[]
     */
    public function readDatabase(Reader $reader): array {
        $this->prepareReader($reader);
        $dates = [ ];

        foreach($reader->getRecords() as $record) {
            $date = new DatabaseDate();
            $date->setCalendarWeek($this->getInt($record[0]));
            $date->setStartDate($this->convertDate($record[2]));
            $date->setSchoolWeek($this->getInt($record[3]));

            $dates[] = $date;
        }

        return $dates;
    }
}