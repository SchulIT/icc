<?php

namespace App\Monolog;

use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\Query\QueryException;
use Monolog\LogRecord;
use SchulIT\CommonBundle\Monolog\ExceptionProcessor;

class SqlAwareExceptionProcessor extends ExceptionProcessor {

    public function __invoke(LogRecord $records): LogRecord {
        $records = parent::__invoke($records);

        if(isset($records['context']['exception']) && $records['context']['exception'] instanceof DriverException) {
            /** @var DriverException $driverException */
            $driverException = $records['context']['exception'];

            if(($query = $driverException->getQuery()) !== null) {
                $records['extra']['exception']['sql'] = $query->getSQL();
                $records['extra']['exception']['params'] = $query->getParams();
            }
        }

        return $records;
    }
}