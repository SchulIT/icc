<?php

namespace App\Untis;

use League\Csv\Reader;

abstract class AbstractGpuReader extends AbstractReader {
    protected function prepareReader(Reader $reader): void {
        $reader->setDelimiter(';');
    }
}