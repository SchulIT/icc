<?php

namespace App\Controller;

use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Request;

trait DateRequestTrait {
    protected function getDateFromRequest(Request $request, string $paramName): ?DateTime {
        if($request->query->has($paramName) === false && $request->request->has($paramName) === false) {
            return null;
        }

        $date = $request->query->get($paramName, $request->request->get($paramName));

        try {
            $dateTime = new DateTime($date);
            $dateTime->setTime(0, 0, 0);

            return $dateTime;
        } catch (Exception $e) {
            return null;
        }
    }
}