<?php

namespace App\Common\Import\EventListener;

use App\Common\Import\Event\TeacherRemoveEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
class AlwaysPreventTeacherRemovalEventListener {
    public function __invoke(TeacherRemoveEvent $event): void {
        $event->preventRemoval = true;
    }
}
