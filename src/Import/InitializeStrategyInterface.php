<?php

namespace App\Import;

interface InitializeStrategyInterface {
    public function initialize(): void;
}