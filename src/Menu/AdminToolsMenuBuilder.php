<?php

namespace App\Menu;

use Knp\Menu\ItemInterface;

class AdminToolsMenuBuilder extends AbstractMenuBuilder {
    public function toolsMenu(array $options = [ ]): ItemInterface {
        $root = $this->factory->createItem('root');

        if($this->authorizationChecker->isGranted('ROLE_TOOLS')) {
            $root->addChild('tools.grade_teacher_intersection.label', [
                'route' => 'grade_tuition_teachers_intersection'
            ])
                ->setExtra('icon', 'fas fa-random');

            $root->addChild('tools.tuition_report.label', [
                'route' => 'tuition_report_tool'
            ]);

            $root->addChild('tools.untis.timetable.label',  [
                'route' => 'untis_timetable_export'
            ]);

            $root->addChild('tools.missing_users.label', [
                'route' => 'missing_users_tool'
            ])
                ->setExtra('icon', 'fas fa-user-times');

            $root->addChild('tools.substitution_report.label', [
                'route' => 'substitution_report_tool'
            ]);
        }

        return $root;
    }
}