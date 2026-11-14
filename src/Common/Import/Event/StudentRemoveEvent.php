<?php

namespace App\Common\Import\Event;

use App\Common\Entity\Student;
use App\Framework\Import\Event\RemoveEvent;

/**
 * @extends  RemoveEvent<Student>
 */
class StudentRemoveEvent extends RemoveEvent {

}
