<?php

namespace App\Controller\Admin;

use App\Entity\Grade;
use App\Form\GradeMembershipType;
use App\Form\GradeTeacherType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GradeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Grade::class;
    }

    public function configureFilters(Filters $filters): Filters {
        return $filters
            ->add('externalId')
            ->add('uuid')
            ->add('name');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Klasse')
            ->setEntityLabelInPlural('Klassen')
            ->setSearchFields(['externalId', 'name', 'id', 'uuid']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('externalId')->setLabel('Externe ID'),
            TextField::new('name')->setLabel('Name'),
            Field::new('allowCollapse')->setLabel('Erlaube Zusammenfassen')->setHelp('Erlaube das Zusammenfassen, bspw. zu 05ABC'),
            CollectionField::new('memberships')
                ->setEntryType(GradeMembershipType::class)
                ->allowAdd(true)
                ->allowDelete(true)
                ->setFormTypeOption('by_reference', false)
                ->setLabel('Mitgliedschaften'),
            CollectionField::new('teachers')
                ->setEntryType(GradeTeacherType::class)
                ->allowAdd(true)
                ->allowDelete(true)
                ->setFormTypeOption('by_reference', false)
                ->setLabel('Klassenleitungen')
        ];
    }
}
