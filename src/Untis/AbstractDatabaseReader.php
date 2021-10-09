<?php

namespace App\Untis;

use League\Csv\Reader;

abstract class AbstractDatabaseReader extends AbstractReader {
    protected function prepareReader(Reader $reader): void {
        $reader->setDelimiter("\t");
    }
}