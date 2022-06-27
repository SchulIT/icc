<?php

namespace App\Untis\Database\Timetable;

use App\Untis\Database\AbstractDatabaseReader;
use App\Untis\Database\Timetable\TimetableLesson;
use League\Csv\Reader;

class TimetableReader extends AbstractDatabaseReader {

    /**
     * @param Reader $reader
     * @return TimetableLesson[]
     */
    public function readDatabase(Reader $reader): array {
        $this->prepareReader($reader);
        $lessons = [ ];

        foreach($reader->getRecords() as $record) {
            $teacher = $this->getStringOrNull($record[0]);
            $lessonNumber = $this->getInt($record[2]);
            $subject = $this->getStringOrNull($record[3]);
            $tuitionNumber = $this->getInt($record[5]);
            $type = $this->getInt($record[6]);

            if($teacher !== null && $lessonNumber > 0 && $subject !== null && $tuitionNumber > 0 && $type === 0) {
                $day = $this->getInt($record[1]);
                $room = $this->getStringOrNull($record[4]);
                $grade = $this->getStringOrNull($record[7]);
                $weeks = $this->getWeeksArray($this->getStringOrNull($record[8]));
                //$flag = $this->getInt($record[9]);

                $lessons[] = (new TimetableLesson())
                    ->setDay($day)
                    ->setGrade($grade)
                    ->setLesson($lessonNumber)
                    ->setRoom($room)
                    ->setSubject($subject)
                    ->setTeacher($teacher)
                    ->setTuitionNumber($tuitionNumber)
                    ->setWeeks($weeks);
            }
        }

        return $lessons;
    }

    private function getWeeksArray(?string $input): array {
        if($input === null) {
            return [ ];
        }

        $weeks = [ ];

        for($i = 0; $i < strlen($input); $i++) {
            $char = $input[$i];

            if($char === "1" || $char === "x") {
                $weeks[] = $i+1;
            }
        }

        return $weeks;
    }
}