<?php

namespace App\Controller\Admin;

use App\Entity\StudentLearningManagementSystemInformation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class StudentLearningManagementSystemInformationCrudController extends AbstractCrudController {

    public static function getEntityFqcn(): string {
        return StudentLearningManagementSystemInformation::class;
    }

    public function configureFields(string $pageName): iterable {
        return [
            AssociationField::new('student'),
            AssociationField::new('lms'),
            TextField::new('username')->setFormTypeOption('required', false),
            TextField::new('password')->setFormTypeOption('required', false),
            BooleanField::new('isConsented')->setFormTypeOption('required', false)
        ];
    }
}