<?php

namespace App\Monolog;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\Types;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class DatabaseHandler extends AbstractProcessingHandler {

    private Connection $connection;

    public function __construct(Connection $connection, int $level = Logger::INFO) {
        parent::__construct($level, false);

        $this->connection = $connection;
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
            'details' => serialize($record['extra'])
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
        } catch (Exception $exception) {
            // Logging failed :-/
        }
    }
}