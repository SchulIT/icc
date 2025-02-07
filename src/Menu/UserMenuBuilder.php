<?php

namespace App\Menu;

use App\Entity\User;
use App\Feature\FeatureManager;
use App\Repository\NotificationRepositoryInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use SchulIT\CommonBundle\DarkMode\DarkModeManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserMenuBuilder extends AbstractMenuBuilder {

    public function __construct(private readonly string $idpProfileUrl, private readonly DarkModeManagerInterface $darkModeManager,
                                private readonly NotificationRepositoryInterface $notificationRepository,
                                FactoryInterface $factory, TokenStorageInterface $tokenStorage,
                                AuthorizationCheckerInterface $authorizationChecker, TranslatorInterface $translator, FeatureManager $featureManager) {
        parent::__construct($factory, $tokenStorage, $authorizationChecker, $translator, $featureManager);
    }

    public function userMenu(array $options): ItemInterface {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttributes([
                'class' => 'navbar-nav float-lg-right'
            ]);

        if($this->tokenStorage->getToken() === null) {
            return $menu;
        }

        $user = $this->tokenStorage->getToken()->getUser();

        if(!$user instanceof User) {
            return $menu;
        }

        $displayName = $user->getUsername();

        $userMenu = $menu->addChild('user', [
            'label' => $displayName
        ])
            ->setExtra('icon', 'fa fa-user')
            ->setExtra('menu', 'user')
            ->setExtra('menu-container', '#submenu')
            ->setExtra('pull-right', true);

        $userMenu->addChild('profile.overview.label', [
            'route' => 'profile'
        ])
            ->setExtra('icon', 'fas fa-user');

        $userMenu->addChild('profile.label', [
            'uri' => $this->idpProfileUrl
        ])
            ->setLinkAttribute('target', '_blank')
            ->setExtra('icon', 'fas fa-address-card');

        $label = 'dark_mode.enable';
        $icon = 'fas fa-moon';

        if($this->darkModeManager->isDarkModeEnabled()) {
            $label = 'dark_mode.disable';
            $icon = 'fas fa-sun';
        }

        $userMenu->addChild($label, [
            'route' => 'toggle_darkmode'
        ])
            ->setExtra('icon', $icon);

        // Notifications
        $menu->addChild('label.notifications', [
            'label' => '',
            'route' => 'notifications'
        ])
            ->setExtra('icon', 'fas fa-bullhorn')
            ->setExtra('count', $this->notificationRepository->countUnreadForUser($user));

        $menu->addChild('label.logout', [
            'route' => 'logout',
            'label' => ''
        ])
            ->setExtra('icon', 'fas fa-sign-out-alt')
            ->setAttribute('title', $this->translator->trans('auth.logout'));

        return $menu;
    }
}