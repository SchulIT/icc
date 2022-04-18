<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\EnumField;
use App\Entity\StudyGroup;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FervoEnumBundle\Generated\Form\StudyGroupTypeType;

class StudyGroupCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return StudyGroup::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('StudyGroup')
            ->setEntityLabelInPlural('StudyGroup')
            ->setSearchFields(['externalId', 'name', 'type', 'id', 'uuid']);
    }

    public function configureFields(string $pageName): iterable
    {
        $externalId = TextField::new('externalId');
        $name = TextField::new('name');
        $type = EnumField::new('type')->setFormType(StudyGroupTypeType::class);
        $grades = AssociationField::new('grades');
        $tuitions = AssociationField::new('tuitions');
        $section = AssociationField::new('section');
        $id = IntegerField::new('id', 'ID');
        $uuid = Field::new('uuid');
        $memberships = AssociationField::new('memberships');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$externalId, $name, $type, $grades, $memberships, $tuitions, $section];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$externalId, $name, $type, $id, $uuid, $grades, $memberships, $tuitions, $section];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$externalId, $name, $type, $grades, $tuitions, $section];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$externalId, $name, $type, $grades, $tuitions, $section];
        }
    }
}
