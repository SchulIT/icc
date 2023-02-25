<?php

namespace App\Menu;

use Knp\Menu\ItemInterface;

class AdminSettingsMenuBuilder extends AbstractMenuBuilder {
    public function settingsMenu(array $options = [ ]): ItemInterface {
        $root = $this->factory->createItem('root');

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $root->addChild('admin.settings.general.label', [
                'route' => 'admin_settings_general'
            ]);

            $root->addChild('admin.settings.dashboard.label', [
                'route' => 'admin_settings_dashboard'
            ])
                ->setExtra('icon', 'fas fa-home');

            $root->addChild('admin.settings.notifications.label', [
                'route' => 'admin_settings_notifications'
            ])
                ->setExtra('icon', 'fas fa-bell');

            $root->addChild('admin.settings.timetable.label', [
                'route' => 'admin_settings_timetable'
            ])
                ->setExtra('icon', 'fas fa-clock');

            $root->addChild('admin.settings.substitutions.label', [
                'route' => 'admin_settings_substitutions'
            ])
                ->setExtra('icon', 'fas fa-random');

            $root->addChild('admin.settings.exams.label', [
                'route' => 'admin_settings_exams'
            ])
                ->setExtra('icon', 'fas fa-edit');

            $root->addChild('admin.settings.appointments.label', [
                'route' => 'admin_settings_appointments'
            ])
                ->setExtra('icon', 'far fa-calendar');

            $root->addChild('admin.settings.student_absences.label', [
                'route' => 'admin_settings_absences'
            ])
                ->setExtra('icon', 'fas fa-user-times');

            $root->addChild('admin.settings.book.label', [
                'route' => 'admin_settings_book'
            ])
                ->setExtra('icon', 'fas fa-book-open');

            $root->addChild('admin.settings.import.label', [
                'route' => 'admin_settings_import'
            ])
                ->setExtra('icon', 'fas fa-upload');
        }

        return $root;
    }
}