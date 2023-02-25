<?php

namespace App\Menu;

use Knp\Menu\ItemInterface;

class AdminToolsMenuBuilder extends AbstractMenuBuilder {
    public function toolsMenu(array $options = [ ]): ItemInterface {
        $root = $this->factory->createItem('root');

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $root->addChild('tools.grade_teacher_intersection.label', [
                'route' => 'grade_tuition_teachers_intersection'
            ])
                ->setExtra('icon', 'fas fa-random');
        }

        return $root;
    }
}