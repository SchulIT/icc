<?php

namespace App\Timetable\Event;

use App\Framework\Import\Event\ImportEvent;
use App\Timetable\Entity\TimetableLesson;

/**
 * @method TimetableLesson[] getAdded()
 * @method TimetableLesson[] getUpdated()
 * @method TimetableLesson[] getRemoved()
 */
class TimetableImportEvent extends ImportEvent {

}