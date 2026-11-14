<?php

namespace App\Common\Import\Event;

use App\Common\Entity\Student;
use App\Framework\Import\Event\RemoveEvent;

/**
 * @implements RemoveEvent<Student>
 */
class StudentRemoveEvent extends RemoveEvent {

}
