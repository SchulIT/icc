<?php

namespace App\Notification\EventSubscriber;

use App\Entity\User;
use App\Entity\UserType;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Form\FormBuilder;

readonly class NotifierManager {

    /**
     * @param iterable|NotifierInterface[] $notifiers
     */
    public function __construct(#[AutowireIterator('app.notifications.notifier')] private iterable $notifiers) {

    }

    /**
     * @param UserType $userType
     * @return NotifierInterface[]
     */
    public function getNotifiersForUserType(UserType $userType): array {
        $result = [ ];

        foreach($this->notifiers as $notifier) {
            if(in_array($userType, $notifier::getSupportedRecipientUserTypes())) {
                $result[] = $notifier;
            }
        }

        return $result;
    }

    /**
     * @param User $user
     * @return NotifierInterface[]
     */
    public function getNotifiersForUser(User $user): iterable {
        return $this->getNotifiersForUserType($user->getUserType());
    }

    /**
     * @return string[]
     */
    public function getAllNotificationKeys(): array {
        $result = [ ];

        foreach($this->notifiers as $notifier) {
            $result[] = $notifier::getKey();
        }

        return $result;
    }

    public function getFormBuilderForUser(UserType $userType): FormBuilder {

    }
}