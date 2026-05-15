<?php

namespace App\Framework\Import;

interface InitializeStrategyInterface {

    /**
     * @param object $requestData
     */
    public function initialize($requestData): void;
}