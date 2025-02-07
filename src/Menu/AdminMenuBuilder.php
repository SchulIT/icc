<?php

namespace App\Menu;

use App\Feature\FeatureManager;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminMenuBuilder extends AbstractMenuBuilder {

    public function __construct(private readonly AdminDataMenuBuilder $dataMenuBuilder, private readonly AdminToolsMenuBuilder $toolsMenuBuilder,
                                FactoryInterface $factory, TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker,
                                TranslatorInterface $translator, FeatureManager $featureManager) {
        parent::__construct($factory, $tokenStorage, $authorizationChecker, $translator, $featureManager);
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
            $menu->addChild('admin.label', [
                'route' => 'admin'
            ])
                ->setExtra('icon', 'fas fa-cogs');
        }

        $toolsMenu = $this->toolsMenuBuilder->toolsMenu();
        if($toolsMenu->count() > 0) {
            $menu->addChild('tools.label', [
                'route' => 'tools'
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