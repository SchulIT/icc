<?php

namespace App\Untis\Gpu\Tuition;

use App\Untis\Gpu\AbstractGpuReader;
use App\Untis\Gpu\Tuition\Tuition;
use League\Csv\Reader;

class TuitionReader extends AbstractGpuReader {

    /**
     * @return Tuition[]
     */
    public function readGpu(Reader $reader): array {
        $this->prepareReader($reader);
        $tuitions = [ ];

        foreach($reader->getRecords() as $record) {
            $tuition = new Tuition();
            $tuition->setId($this->getInt($record[0]));
            $tuition->setGrade($record[4]);
            $tuition->setTeacher($record[5]);
            $tuition->setSubject($record[6]);
            $tuition->setRooms($this->getStringArrayOrEmptyArray($record[7]));
            $tuition->setGroup($record[11]);
            $tuition->setValidFrom($this->convertDate($record[14]));
            $tuition->setValidTo($this->convertDate($record[15]));

            $tuitions[] = $tuition;
        }

        return $tuitions;
    }
}