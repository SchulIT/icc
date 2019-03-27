<?php

namespace App\Grouping;

interface GroupInterface {

    public function getKey();

    public function addItem($item);
}