<?php

namespace App\Exam\Event;

use App\Exam\Entity\Exam;
use App\Framework\Import\Event\ImportEvent;

/**
 * @method Exam[] getAdded()
 * @method Exam[] getUpdated()
 * @method Exam[] getRemoved()
 */
class ExamImportEvent extends ImportEvent {

}