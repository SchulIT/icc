<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\MenuProviderInterface;

class Builder {
    private $factory;

    public function __construct(FactoryInterface $factory) {
        $this->factory = $factory;
    }

    private function plansMenu(ItemInterface $menu): ItemInterface {
        $plans = $menu->addChild('plans.label')
            ->setAttribute('dropdown', true)
            ->setChildrenAttribute('id', 'menu-plans');

        $plans->addChild('plans.timetable.label', [
            'route' => 'timetable'
        ]);

        $plans->addChild('plans.substitutions.label', [

        ]);

        $plans->addChild('plans.exams.label', [
            'route' => 'exams'
        ]);

        $plans->addChild('plans.appointments.label', [

        ]);

        $plans->addChild('plans.rooms.label', [

        ]);

        return $plans;
    }

    private function listsMenu(ItemInterface $menu): ItemInterface {
        $lists = $menu->addChild('lists.label')
            ->setAttribute('dropdown', true)
            ->setChildrenAttribute('id', 'menu-lists');

        $lists->addChild('lists.tuitions.label', [
            'route' => 'list_tuitions'
        ]);

        $lists->addChild('lists.study_groups.label', [
            'route' => 'list_studygroups'
        ]);

        return $lists;
    }

    public function mainMenu(array $options) {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttribute('class', 'nav nav-pills flex-column');

        $menu->addChild('dashboard.label', [
            'route' => 'dashboard'
        ]);

        $this->plansMenu($menu);
        $this->listsMenu($menu);

        return $menu;
    }
}