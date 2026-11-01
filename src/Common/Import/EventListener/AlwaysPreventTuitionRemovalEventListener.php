<?php

namespace App\Common\Import\EventListener;

use App\Common\Import\Event\TuitionRemoveEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
class AlwaysPreventTuitionRemovalEventListener {
    public function __invoke(TuitionRemoveEvent $event): void {
        $event->preventRemoval = true;
    }
}
