<?php

namespace App\Event;

use App\Entity\ReturnItem;
use Symfony\Contracts\EventDispatcher\Event;

class ReturnItemReturnedEvent extends Event {
    public function __construct(private readonly ReturnItem $returnItem) {

    }

    public function getReturnItem(): ReturnItem {
        return $this->returnItem;
    }
}