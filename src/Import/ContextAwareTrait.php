<?php

namespace App\Import;

use DateTime;

trait ContextAwareTrait {
    protected function getContext($requestData): ?DateTime {
        if(empty($requestData->getContext())) {
            return null;
        }

        $dateTime = DateTime::createFromFormat('Y-m-d', $requestData->getContext());

        if($dateTime === false) {
            throw new ImportException(sprintf('Kontext "%s" hat das falsche Format (erwartet: YYYY-MM-DD).', $requestData->getContext()));
        }

        $dateTime->setTime(0,0,0);

        return $dateTime;
    }
}