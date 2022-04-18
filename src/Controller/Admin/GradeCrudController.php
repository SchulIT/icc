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
        $id = IntegerField::new('id', 'ID');
        $uuid = Field::new('uuid');
        $memberships = AssociationField::new('memberships');
        $teachers = AssociationField::new('teachers');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$externalId, $name, $allowCollapse, $id, $memberships, $teachers];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$externalId, $name, $allowCollapse, $id, $uuid, $memberships, $teachers];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$externalId, $name, $allowCollapse];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$externalId, $name, $allowCollapse];
        }
    }
}
