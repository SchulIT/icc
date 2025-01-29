<?php

namespace App\Notification\Delivery;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum DeliverStrategyType: string implements TranslatableInterface {
    case OptIn = 'opt_in';
    case OptOut = 'opt_out';

    case Always = 'always';

    case Never = 'never';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string {
        return $translator->trans(sprintf('notifications.strategy.%s', $this->value), locale: $locale);
    }
}