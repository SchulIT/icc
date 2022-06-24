<?php

namespace App\Controller\Admin;

use App\Entity\Grade;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GradeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Grade::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Grade')
            ->setEntityLabelInPlural('Grade')
            ->setSearchFields(['externalId', 'name', 'id', 'uuid']);
    }

    public function configureFields(string $pageName): iterable
    {
        $externalId = TextField::new('externalId');
        $name = TextField::new('name');
        $allowCollapse = Field::new('allowCollapse');
        $id = IntegerField::new('id', 'ID')->hideOnForm();
        $memberships = AssociationField::new('memberships');
        $teachers = AssociationField::new('teachers');

        return [$id, $externalId, $name, $allowCollapse, $memberships, $teachers];
    }
}
