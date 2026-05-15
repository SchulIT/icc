<?php

namespace App\Infrastructure\Monolog;

use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\Query\QueryException;
use Monolog\LogRecord;
use SchulIT\CommonBundle\Monolog\ExceptionProcessor;

class SqlAwareExceptionProcessor extends ExceptionProcessor {

    public function __invoke(LogRecord $record): LogRecord {
        $record = parent::__invoke($record);

        if(isset($record['context']['exception']) && $record['context']['exception'] instanceof DriverException) {
            /** @var DriverException $driverException */
            $driverException = $record['context']['exception'];

            if(($query = $driverException->getQuery()) !== null) {
                $record['extra']['exception']['sql'] = $query->getSQL();
                $record['extra']['exception']['params'] = $query->getParams();
            }
        }

        return $record;
    }
}