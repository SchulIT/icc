<?php

namespace App\Controller\Admin;

use App\Entity\LearningManagementSystem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class LearningManagementSystemCrudController extends AbstractCrudController {
    public static function getEntityFqcn(): string {
        return LearningManagementSystem::class;
    }

    public function configureFilters(Filters $filters): Filters {
        $filters->add('name');
        return $filters;
    }

    public function configureFields(string $pageName): iterable {
        return [
            TextField::new('externalId'),
            TextField::new('name')
        ];
    }
}