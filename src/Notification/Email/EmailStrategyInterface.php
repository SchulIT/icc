<?php

namespace App\Notification\Email;

use App\Notification\Notification;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.notifications.email_strategy')]
interface EmailStrategyInterface {

    /**
     * Returns whether this strategy supports the given notification. This controls
     * whether this strategy is executed.
     *
     * @param Notification $notification
     * @return bool
     */
    public function supports(Notification $notification): bool;

    /**
     * Returns the Reply-To email address (if any is given)
     *
     * @param Notification $notification The object which is the objective of the notification.
     * @return string|null
     */
    public function getReplyTo(Notification $notification): ?string;

    /**
     * Returns the name of the sender, which signs the email
     *
     * @param Notification $notification
     * @return string
     */
    public function getSender(Notification $notification): string;

    /**
     * Returns the template which will be rendered as content
     *
     * @return string
     */
    public function getTemplate(): string;

    /**
     * Returns the template which will be rendered for HTML content (optional)
     *
     * @return string|null
     */
    public function getHtmlTemplate(): ?string;
}