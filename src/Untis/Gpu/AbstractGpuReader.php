<?php

namespace App\Untis\Gpu;

use App\Untis\AbstractReader;
use League\Csv\Reader;

abstract class AbstractGpuReader extends AbstractReader {
    protected function prepareReader(Reader $reader): void {
        $reader->setDelimiter(';');
    }
}