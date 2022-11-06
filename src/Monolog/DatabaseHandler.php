<?php

namespace App\Monolog;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\Types;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class DatabaseHandler extends AbstractProcessingHandler {

    public function __construct(private Connection $connection, int $level = Logger::INFO) {
        parent::__construct($level, false);
    }

    /**
     * @inheritDoc
     */
    protected function write(array $record): void {
        $entry = [
            'channel' => $record['channel'],
            'level' => $record['level'],
            'message' => $record['formatted'],
            'time' => $record['datetime'],
            'details' => json_encode($record['extra'], JSON_PRETTY_PRINT)
        ];

        try {
            $this->connection
                ->insert('log', $entry, [
                    Types::STRING,
                    Types::INTEGER,
                    Types::TEXT,
                    Types::DATETIME_MUTABLE,
                    Types::STRING
                ]);
        } catch (Exception) {
            // Logging failed :-/
        }
    }
}