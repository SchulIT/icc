<?php

namespace App\Monolog;

use Monolog\Processor\ProcessorInterface;
use Throwable;

class ExceptionProcessor implements ProcessorInterface {

    public function __invoke(array $records): array {
        if(isset($records['context']['exception']) && $records['context']['exception'] instanceof Throwable) {
            $records['extra']['exception'] = [
                'class' => get_class($records['context']['exception']),
                'message' => $records['context']['exception']->getMessage(),
                'stacktrace' => $records['context']['exception']->getTraceAsString()
            ];
        }

        return $records;
    }
}