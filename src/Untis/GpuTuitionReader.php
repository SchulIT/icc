<?php

namespace App\Untis;

use League\Csv\Reader;

class GpuTuitionReader extends AbstractGpuReader {

    /**
     * @param Reader $reader
     * @return GpuTuition[]
     */
    public function readGpu(Reader $reader): array {
        $this->prepareReader($reader);
        $tuitions = [ ];

        foreach($reader->getRecords() as $record) {
            $tuition = new GpuTuition();
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