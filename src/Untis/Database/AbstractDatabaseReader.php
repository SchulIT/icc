<?php

namespace App\Untis\Database;

use App\Untis\AbstractReader;
use League\Csv\Reader;

abstract class AbstractDatabaseReader extends AbstractReader {
    protected function prepareReader(Reader $reader): void {
        $reader->setDelimiter("\t");
    }
}