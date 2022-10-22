<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Throwable;

class InvalidAccountException extends AccountStatusException {
    public function __construct(private string $messageKey, $message = "", $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function getMessageKey(): string {
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