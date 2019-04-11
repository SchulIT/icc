<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;

class Builder {
    private $factory;

    public function __construct(FactoryInterface $factory) {
        $this->factory = $factory;
    }

    public function mainMenu(array $options) {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttribute('class', 'nav nav-pills flex-column');

        $menu->addChild('menu.label', [
            'attributes' => [
                'class' => 'header'
            ]
        ]);

        $menu->addChild('dashboard.label', [
            'route' => 'dashboard'
        ]);

        return $menu;
    }
}