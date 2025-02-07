<?php

namespace App\Menu;

use App\Feature\FeatureManager;
use Knp\Menu\FactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractMenuBuilder {
    public function __construct(protected readonly FactoryInterface $factory,
                                protected readonly TokenStorageInterface $tokenStorage,
                                protected readonly AuthorizationCheckerInterface $authorizationChecker,
                                protected readonly TranslatorInterface $translator,
                                protected readonly FeatureManager $featureManager) { }
}