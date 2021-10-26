<?php

namespace App\Untis;

use League\Csv\Reader;

class GpuExamReader extends AbstractGpuReader {

    /**
     * @param Reader $reader
     * @return GpuExam[]
     */
    public function readGpu(Reader $reader): array {
        $this->prepareReader($reader);
        $exams = [ ];

        foreach($reader->getRecords() as $record) {
            $exam = new GpuExam();
            $exam->setName($this->getStringOrNull($record[0]));
            $exam->setId($this->getInt($record[1]));
            $exam->setText($this->getStringOrNull($record[2]));
            $exam->setDate($this->convertDate($record[3]));
            $exam->setLessonStart($this->getInt($record[4]));
            $exam->setLessonEnd($this->getInt($record[5]));
            $exam->setSubjects($this->getStringArrayOrEmptyArray($record[6]));
            $exam->setTuitions($this->getIntArrayOrEmptyArray($record[7]));
            $exam->setStudents($this->getStringArrayOrEmptyArray($record[8]));
            $exam->setSupervisions($this->getStringArrayOrEmptyArray($record[9], '-'));
            $exam->setRooms($this->getStringArrayOrEmptyArray($record[10], '-'));

            $exams[] = $exam;
        }

        return $exams;
    }
}