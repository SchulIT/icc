<?php

namespace App\Framework\Import;

use App\Framework\Import\ImportResult;

interface PostActionStrategyInterface {
    public function onFinished(ImportResult $result);
}