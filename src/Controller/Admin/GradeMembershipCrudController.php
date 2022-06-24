<?php

namespace App\Controller\Admin;

use App\Entity\GradeMembership;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class GradeMembershipCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GradeMembership::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('GradeMembership')
            ->setEntityLabelInPlural('GradeMembership')
            ->setSearchFields(['id']);
    }

    public function configureFields(string $pageName): iterable
    {
        $student = AssociationField::new('student');
        $grade = AssociationField::new('grade');
        $section = AssociationField::new('section');
        $id = IntegerField::new('id', 'ID')->hideOnForm();

        return [$id, $student, $grade, $section];
    }
}
