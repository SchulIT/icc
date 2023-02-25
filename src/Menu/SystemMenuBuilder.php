<?php

namespace App\Menu;

use Knp\Menu\ItemInterface;

class SystemMenuBuilder extends AbstractMenuBuilder {
    public function systemMenu(array $options): ItemInterface {
        $root = $this->factory->createItem('root')
            ->setChildrenAttributes([
                'class' => 'navbar-nav float-lg-right'
            ]);

        $menu = $root->addChild('system', [
            'label' => ''
        ])
            ->setExtra('icon', 'fa fa-tools')
            ->setExtra('menu', 'system')
            ->setExtra('menu-container', '#submenu')
            ->setExtra('pull-right', true)
            ->setAttribute('title', $this->translator->trans('system.label'));

        if($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $menu->addChild('cron.label', [
                'route' => 'admin_cronjobs'
            ])
                ->setExtra('icon', 'fas fa-history');

            $menu->addChild('logs.label', [
                'route' => 'admin_logs'
            ])
                ->setExtra('icon', 'fas fa-clipboard-list');

            $menu->addChild('messenger.label', [
                'route' => 'admin_messenger'
            ])
                ->setExtra('icon', 'fas fa-envelope-open-text');

            $menu->addChild('audit.label', [
                'uri' => '/admin/audit'
            ])
                ->setLinkAttribute('target', '_blank')
                ->setExtra('icon', 'far fa-eye');
        }

        return $root;
    }
}