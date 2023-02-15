<?php

namespace App\Controller\Admin;

use App\Entity\Tuition;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class TuitionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tuition::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Unterrichte')
            ->setEntityLabelInPlural('Unterricht')
            ->setSearchFields(['externalId', 'name', 'displayName', 'id', 'uuid']);
    }

    public function configureFilters(Filters $filters): Filters {
        return $filters
            ->add('section')
            ->add('subject')
            ->add(EntityFilter::new('studyGroup')->canSelectMultiple(true));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('externalId')->setLabel('Externe ID'),
            TextField::new('name')->setLabel('Name'),
            TextField::new('displayName')
                ->setLabel('Anzeigename'),
            AssociationField::new('subject')->setLabel('Fach'),
            AssociationField::new('teachers')->setLabel('Lehrkräfte'),
            AssociationField::new('studyGroup')->setLabel('Lerngruppe'),
            AssociationField::new('section')->setLabel('Abschnitt')->setFormTypeOption('expanded', true),
            BooleanField::new('isBookEnabled')->setLabel('Unterrichtsbuch aktiv')
        ];
    }
}
