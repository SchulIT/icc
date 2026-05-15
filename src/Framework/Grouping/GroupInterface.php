<?php

namespace App\Framework\Grouping;

interface GroupInterface {

    public function getKey();

    public function addItem($item);
}