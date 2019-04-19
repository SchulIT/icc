<?php

namespace App\Converter;

use App\Entity\UserType;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserTypeStringConverter {

    private $translator;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    public function convert(UserType $userType) {
        return $this->translator->trans(
            sprintf('usertype.%s', strtolower($userType->getValue()))
        );
    }
}