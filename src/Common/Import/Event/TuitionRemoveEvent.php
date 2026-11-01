<?php

namespace App\Common\Import\Event;

use App\Common\Entity\Tuition;
use App\Framework\Import\Event\RemoveEvent;

/**
 * @implements RemoveEvent<Tuition>
 */
class TuitionRemoveEvent extends RemoveEvent {

}
