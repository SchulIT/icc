<?php

namespace App\Controller\Admin;

use App\Entity\Tuition;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TuitionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tuition::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Tuition')
            ->setEntityLabelInPlural('Tuition')
            ->setSearchFields(['externalId', 'name', 'displayName', 'id', 'uuid']);
    }

    public function configureFields(string $pageName): iterable
    {
        $externalId = TextField::new('externalId');
        $name = TextField::new('name');
        $displayName = TextField::new('displayName');
        $subject = AssociationField::new('subject');
        $teachers = AssociationField::new('teachers');
        $studyGroup = AssociationField::new('studyGroup');
        $id = IntegerField::new('id', 'ID');
        $uuid = Field::new('uuid');
        $section = AssociationField::new('section');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$externalId, $name, $displayName, $id, $subject, $teachers, $studyGroup];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$externalId, $name, $displayName, $id, $uuid, $subject, $teachers, $studyGroup, $section];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$externalId, $name, $displayName, $subject, $teachers, $studyGroup];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$externalId, $name, $displayName, $subject, $teachers, $studyGroup];
        }
    }
}
