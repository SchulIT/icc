<?php

namespace App\Notification\Email;

use App\Entity\User;

interface EmailStrategyInterface {

    /**
     * Returns whether this strategy is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Returns whether this strategy supports the given objective. This controls
     * whether or not this strategy is executed.
     *
     * @param object $objective
     * @return bool
     */
    public function supports($objective): bool;

    /**
     * Returns the Reply-To email address (if any is given)
     *
     * @param object $objective The object which is the objective of the notification.
     * @return string|null
     */
    public function getReplyTo($objective): ?string;

    /**
     * @param object $objective The object which is the objective of the notification.
     * @return User[]
     */
    public function getRecipients($objective): array;

    /**
     * Returns the translation key for the subject of the email
     *
     * @param object $objective The object which is the objective of the notification.
     * @return string
     */
    public function getSubject($objective): string;

    /**
     * Returns the name of the sender, which signs the email
     *
     * @param object $objective
     * @return string
     */
    public function getSender($objective): string;

    /**
     * Returns the template which will be rendered as content
     *
     * @return string
     */
    public function getTemplate(): string;
}