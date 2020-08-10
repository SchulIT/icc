<?php

namespace App\Import;

interface PostActionStrategyInterface {
    public function onFinished(ImportResult $result);
}