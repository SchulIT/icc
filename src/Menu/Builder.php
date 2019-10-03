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
        $plans = $menu->addChild('plans.label', [
            'attributes' => [
                'class' => 'header'
            ]
        ]);

        $menu->addChild('plans.timetable.label', [
            'route' => 'timetable'
        ]);

        $menu->addChild('plans.substitutions.label', [
            'route' => 'substitutions'
        ]);

        $menu->addChild('plans.exams.label', [
            'route' => 'exams'
        ]);

        $menu->addChild('plans.appointments.label', [
            'route' => 'appointments'
        ]);

        $menu->addChild('plans.rooms.label', [
            'route' => 'rooms'
        ]);

        return $plans;
    }

    private function listsMenu(ItemInterface $menu): ItemInterface {
        $lists = $menu->addChild('lists.label', [
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

        $menu->addChild('lists.teachers.label', [
            'route' => 'list_teachers'
        ]);

        return $lists;
    }

    private function adminMenu(ItemInterface $menu): ItemInterface {
        $admin = $menu->addChild('admin.label', [
            'attributes' => [
                'class' => 'header'
            ]
        ])
            ->setAttribute('dropdown', true)
            ->setChildrenAttribute('id', 'menu-admin');

        $admin->addChild('admin.documents.label', [
            'route' => 'admin_documents'
        ]);

        $admin->addChild('admin.messages.label', [
            'route' => 'admin_messages'
        ]);

        $admin->addChild('admin.appointments.label', [
            'route' => 'admin_appointments'
        ]);

        return $admin;
    }

    private function settingsMenu(ItemInterface $menu): ItemInterface {
        $settings = $menu->addChild('admin.settings.label', [
            'attributes' => [
                'class' => 'header'
            ]
        ])
            ->setAttribute('dropdown', true)
            ->setChildrenAttribute('id', 'menu-settings');

        $settings->addChild('admin.settings.timetable.label', [
            'route' => 'admin_settings_timetable'
        ]);

        $settings->addChild('admin.timetable.weeks.label', [
            'route' => 'admin_timetable_weeks'
        ]);

        $settings->addChild('admin.timetable.periods.label', [
            'route' => 'admin_timetable_periods'
        ]);

        $settings->addChild('admin.settings.exams.label', [
            'route' => 'admin_settings_exams'
        ]);

        return $settings;
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

        $menu->addChild('documents.label', [
            'route' => 'documents'
        ]);

        $menu->addChild('messages.overview.label', [
            'route' => 'messages'
        ]);

        $this->plansMenu($menu);
        $this->listsMenu($menu);
        $this->settingsMenu($menu);
        $this->adminMenu($menu);

        return $menu;
    }
}