<?php

namespace App\Notification\Email;

use App\Entity\User;

interface EmailStrategyInterface {

    /**
     * Returns the Reply-To email address (if any is given)
     *
     * @return string|null
     */
    public function getReplyTo(): ?string;

    /**
     * @return User[]
     */
    public function getUserEnrolledForNotification();

    /**
     * Returns the translation key for the subject of the email
     *
     * @return string
     */
    public function getSubject(): string;

    /**
     * Returns the template which will be rendered as content
     *
     * @return string
     */
    public function getTemplate(): string;
}