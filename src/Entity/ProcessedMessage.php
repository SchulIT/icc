<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zenstruck\Messenger\Monitor\History\Model\ProcessedMessage as BaseProcessedMessage;

#[ORM\Entity(readOnly: true)]
#[ORM\Table('processed_messages')]
class ProcessedMessage extends BaseProcessedMessage {
    use IdTrait;

    public function id(): string|int|\Stringable|null {
        return $this->id;
    }
}