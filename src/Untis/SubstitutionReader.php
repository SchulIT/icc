<?php

namespace App\Untis;

use League\Csv\Reader;
use UnexpectedValueException;

class SubstitutionReader extends AbstractGpuReader {
    private function convertType(string $value): ?GpuSubstitutionType {
        $value = trim($value);

        try {
            return GpuSubstitutionType::from($value);
        } catch (UnexpectedValueException $e) { }

        return null;
    }

    /**
     * @return GpuSubstitution[]
     */
    public function readGpu(Reader $reader): array {
        $this->prepareReader($reader);
        $substitutions = [ ];

        foreach($reader->getRecords() as $record) {
            $substitution = new GpuSubstitution();
            $substitution->setId(intval($record[0]));
            $substitution->setDate($this->convertDate($record[1]));
            $substitution->setLesson(intval($record[2]));
            $substitution->setTeacher($this->getStringOrNull($record[5]));
            $substitution->setReplacementTeacher($this->getStringOrNull($record[6]));
            $substitution->setSubject($this->getStringOrNull($record[7]));
            $substitution->setReplacementSubject($this->getStringOrNull($record[9]));
            $substitution->setRooms($this->getStringArrayOrEmptyArray($record[11]));
            $substitution->setReplacementRooms($this->getStringArrayOrEmptyArray($record[12]));
            $substitution->setGrades($this->getStringArrayOrEmptyArray($record[14]));
            $substitution->setReplacementGrades($this->getStringArrayOrEmptyArray($record[18]));
            $substitution->setRemark($this->getStringOrNull($record[16]));
            $substitution->setFlags(intval($record[17]));
            $substitution->setType($this->convertType($record[19]));
            $substitution->setLastChange($this->convertDateTime($record[20]));

            $substitutions[] = $substitution;
        }

        return $substitutions;
    }
}