<?php

namespace App\Substitution\Event;

use App\Framework\Import\Event\ImportEvent;
use App\Substitution\Entity\Substitution;

/**
 * @method Substitution[] getAdded()
 * @method Substitution[] getUpdated()
 * @method Substitution[] getRemoved()
 */
class SubstitutionImportEvent extends ImportEvent {

}