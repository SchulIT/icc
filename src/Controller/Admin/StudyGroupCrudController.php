<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\EnumField;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class StudyGroupCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return StudyGroup::class;
    }

    public function configureFilters(Filters $filters): Filters {
        return $filters
            ->add('uuid')
            ->add('externalId')
            ->add('type')
            ->add('grades');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Lerngruppen')
            ->setEntityLabelInPlural('Lerngruppe')
            ->setSearchFields(['externalId', 'name', 'type', 'id', 'uuid']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('externalId')->setLabel('Externe ID'),
            TextField::new('name')->setLabel('Name'),
            ChoiceField::new('type')
                ->setChoices(StudyGroupType::cases())
                ->setLabel('Art'),
            AssociationField::new('grades')->setLabel('Klassen'),
            AssociationField::new('section')->setLabel('Abschnitt')->setFormTypeOption('expanded', true)
        ];
    }
}
