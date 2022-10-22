<?php

namespace App\Grouping;

use App\Entity\Message;
use SchulIT\CommonBundle\Helper\DateHelper;

class MessageExpirationStrategy implements GroupingStrategyInterface {

    public function __construct(private DateHelper $dateHelper)
    {
    }

    /**
     * @param Message $object
     * @return bool
     */
    public function computeKey($object, array $options = [ ]) {
        return $object->getExpireDate() < $this->dateHelper->getNow();
    }

    /**
     * @param bool $keyA
     * @param bool $keyB
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        return $keyA === $keyB;
    }

    /**
     * @param bool $key
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        return new MessageExpirationGroup($key);
    }
}