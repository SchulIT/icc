<?php

namespace App\Import;

interface InitializeStrategyInterface {

    /**
     * @param object $requestData
     */
    public function initialize($requestData): void;
}