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

        // Plans
        $menu->addChild('plans.label', [
            'attributes' => [
                'class' => 'header'
            ]
        ]);

        $menu->addChild('plans.exams.label', [
            'route' => 'exams'
        ]);


        // Lists menu
        $menu->addChild('lists.label', [
            'attributes' => [
                'class' => 'header'
            ]
        ]);

        $menu->addChild('lists.tuitions.label', [
            'route' => 'list_tuitions'
        ]);

        $menu->addChild('lists.study_groups.label', [
            'route' => 'list_studygroups'
        ]);

        return $menu;
    }
}