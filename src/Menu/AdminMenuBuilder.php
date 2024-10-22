<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminMenuBuilder extends AbstractMenuBuilder {

    public function __construct(private readonly AdminDataMenuBuilder $dataMenuBuilder, private readonly AdminToolsMenuBuilder $toolsMenuBuilder,
                                FactoryInterface $factory, TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker, TranslatorInterface $translator) {
        parent::__construct($factory, $tokenStorage, $authorizationChecker, $translator);
    }

    public function adminMenu(array $options): ItemInterface {
        $root = $this->factory->createItem('root')
            ->setChildrenAttributes([
                'class' => 'navbar-nav float-lg-right'
            ]);

        $menu = $root->addChild('admin', [
            'label' => ''
        ])
            ->setExtra('icon', 'fa fa-cogs')
            ->setAttribute('title', $this->translator->trans('admin.label'))
            ->setExtra('menu', 'admin')
            ->setExtra('menu-container', '#submenu')
            ->setExtra('pull-right', true);

        $dataMenu = $this->dataMenuBuilder->dataMenu();

        if($dataMenu->count() > 0) {
            $firstKey = array_key_first($dataMenu->getChildren());
            $first = $dataMenu->getChildren()[$firstKey];

            $menu->addChild('admin.label', [
                'route' => 'admin'
            ])
                ->setExtra('icon', 'fas fa-cogs');
        }

        $toolsMenu = $this->toolsMenuBuilder->toolsMenu();
        if($toolsMenu->count() > 0) {
            $firstKey = array_key_first($toolsMenu->getChildren());
            $first = $toolsMenu->getChildren()[$firstKey];

            $menu->addChild('tools.label', [
                'uri' => $first->getUri()
            ])
                ->setExtra('icon', 'fas fa-toolbox');
        }

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $menu->addChild('api.doc', [
                'uri' => '/docs/api/import'
            ])
                ->setExtra('icon', 'fas fa-code');
        }

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