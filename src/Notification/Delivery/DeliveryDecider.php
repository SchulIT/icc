<?php

namespace App\Notification\Delivery;

use App\Entity\User;
use App\Notification\NotificationDeliveryTarget;
use App\Repository\UserNotificationSettingRepositoryInterface;
use App\Settings\NotificationSettings;
use App\Utils\ArrayUtils;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

readonly class DeliveryDecider {

    /** @var DeliveryStrategy[] */
    private array $strategies;

    public function __construct(private UserNotificationSettingRepositoryInterface $userNotificationSettingRepository,
                                private NotificationSettings $settings,
                                #[AutowireIterator('app.notifications.delivery_strategy')] iterable $strategies) {
        $this->strategies = ArrayUtils::createArrayWithKeys($strategies, fn(DeliveryStrategy $strategy) => $strategy->getStrategyType()->value);
    }

    public function decide(User $user, string $type, NotificationDeliveryTarget $target): bool {
        $strategyType = $this->settings->getDeliveryStrategy($user->getUserType(), $type, $target);
        $strategy = $this->strategies[$strategyType->value] ?? null;

        if($strategy === null) {
            // THROW ERROR
            return false;
        }

        $setting = $this->userNotificationSettingRepository->findByUserAndTypeAndTarget($user, $type, $target);
        return $strategy->deliver($setting);
    }
}