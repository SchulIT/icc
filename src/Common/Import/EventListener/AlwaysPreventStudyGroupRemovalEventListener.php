<?php

namespace App\Common\Import\EventListener;

use App\Common\Import\Event\StudyGroupRemoveEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
class AlwaysPreventStudyGroupRemovalEventListener {
    public function __invoke(StudyGroupRemoveEvent $event): void {
        $event->preventRemoval = true;
    }
}
