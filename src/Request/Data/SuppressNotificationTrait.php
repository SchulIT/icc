<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;

trait SuppressNotificationTrait {

    /**
     * If this flag is set to true, no notifications are send after a successful import
     * @Serializer\Type("boolean")
     * @Serializer\SerializedName("suppress_notifications")
     */
    private $suppressNotifications = false;

    /**
     * @return bool
     */
    public function isSuppressNotifications(): bool {
        return $this->suppressNotifications;
    }

    /**
     * @param bool $suppressNotifications
     * @return self
     */
    public function setSuppressNotifications(bool $suppressNotifications): self {
        $this->suppressNotifications = $suppressNotifications;
        return $this;
    }
}