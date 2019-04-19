<?php

namespace App\Converter;

use App\Entity\MessageScope;
use Symfony\Contracts\Translation\TranslatorInterface;

class MessageScopeStringConverter {

    private $translator;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    public function convert(MessageScope $scope) {
        return $this->translator->trans(
            sprintf('message.scopes.%s', strtolower($scope->getValue()))
        );
    }
}