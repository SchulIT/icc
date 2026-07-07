<?php

namespace App\Common\Entity;

use Override;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum ChairType: string implements TranslatableInterface {
    case Primary = 'primary';
    case Substitute = 'substitute';

    #[Override]
    public function trans(TranslatorInterface $translator, ?string $locale = null): string {
        return $translator->trans(
            sprintf('chair_type.%s', $this->value),
            locale: $locale,
            domain: 'enums'
        );
    }
}
