<?php

namespace App\Menu;

use Knp\Menu\ItemInterface;

class ImportMenuBuilder extends AbstractMenuBuilder {

    public function importMenu(array $options = [ ]): ItemInterface {
        $root = $this->factory->createItem('root')
            ->setChildrenAttributes([
                'class' => 'navbar-nav float-lg-right'
            ]);

        $menu = $root->addChild('import', [
            'label' => ''
        ])
            ->setExtra('icon', 'fas fa-upload')
            ->setExtra('menu', 'import')
            ->setExtra('menu-container', '#submenu')
            ->setExtra('pull-right', true)
            ->setAttribute('title', $this->translator->trans('import.label'));

        if($this->authorizationChecker->isGranted('ROLE_IMPORTER')) {
            $menu->addChild('import.settings.label', [
                'route' => 'import_untis_settings'
            ])
                ->setExtra('icon', 'fas fa-cogs');

            $menu->addChild('import.html_settings.label', [
                'route' => 'import_untis_html_settings'
            ])
                ->setExtra('icon', 'fas fa-cogs');

            $menu->addChild('import.substitutions.gpu.label', [
                'route' => 'import_untis_substitutions_gpu'
            ])
                ->setExtra('icon', 'fas fa-random');

            $menu->addChild('import.substitutions.html.label', [
                'route' => 'import_untis_substitutions_html'
            ])
                ->setExtra('icon', 'fas fa-random');

            $menu->addChild('import.exams.label', [
                'route' => 'import_untis_exams'
            ])
                ->setExtra('icon', 'fas fa-edit');

            $menu->addChild('import.supervisions.label', [
                'route' => 'import_untis_supervisions'
            ])
                ->setExtra('icon', 'fas fa-eye');

            $menu->addChild('import.rooms.label', [
                'route' => 'import_untis_rooms'
            ])
                ->setExtra('icon', 'fas fa-door-open');

            $menu->addChild('import.timetable.html.label', [
                'route' => 'import_untis_timetable_html'
            ])
                ->setExtra('icon', 'far fa-clock');
        }

        return $root;
    }
}