<?php

namespace App\Monolog;

use Monolog\Formatter\FormatterInterface;

class LineFormatter implements FormatterInterface {

    /**
     * @inheritDoc
     */
    public function format(array $record) {
        $line = $record['message'];

        foreach($record['context'] as $key => $value) {
            $line = str_replace('{' . $key . '}', is_scalar($value) ? $value : json_encode($value), $line);
        }

        return $line;
    }

    /**
     * @inheritDoc
     */
    public function formatBatch(array $records) {
        foreach($records as $key => $record) {
            $records[$key] = $this->format($record);
        }

        return $records;
    }
}