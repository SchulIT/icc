<?php

namespace App\Notification\EventSubscriber;

use App\Entity\UserType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.notifications.notifier')]
interface NotifierInterface {

    /**
     * @return UserType[] User types which this notifier interface supports as recipients (also used in GUI to display or hide delivery options)
     */
    public static function getSupportedRecipientUserTypes(): array;

    /**
     * @return string Unique name used to store delivery options related to this notifier interface.
     */
    public static function getKey(): string;

    /**
     * @return string The message key used for translations related to this notification type
     */
    public static function getLabelKey(): string;

    /**
     * @return string The message key used for translating a help text related to this notification type
     */
    public static function getHelpKey(): string;
}