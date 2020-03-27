<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Throwable;

class InvalidAccountException extends AccountStatusException {
    private $messageKey;

    public function __construct(string $messageKey, $message = "", $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);

        $this->messageKey = $messageKey;
    }

    public function getMessageKey() {
        return $this->messageKey;
    }

    /**
     * {@inheritdoc}
     */
    public function __serialize(): array
    {
        return [$this->messageKey, parent::__serialize()];
    }

    /**
     * {@inheritdoc}
     */
    public function __unserialize(array $data): void
    {
        [$this->messageKey, $parentData] = $data;
        parent::__unserialize($parentData);
    }
}