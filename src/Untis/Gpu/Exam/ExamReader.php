<?php

namespace App\Untis\Gpu\Exam;

use App\Untis\Gpu\AbstractGpuReader;
use League\Csv\Reader;

class ExamReader extends AbstractGpuReader {

    /**
     * @return Exam[]
     */
    public function readGpu(Reader $reader): array {
        $this->prepareReader($reader);
        $exams = [ ];

        foreach($reader->getRecords() as $record) {
            $exam = new Exam();
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