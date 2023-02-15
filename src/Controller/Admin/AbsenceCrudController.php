<?php

namespace App\Controller\Admin;

use App\Entity\Absence;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class AbsenceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Absence::class;
    }

    public function configureFilters(Filters $filters): Filters {
        return $filters
            ->add('date')
            ->add('teacher')
            ->add('studyGroup')
            ->add('room');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Absenz')
            ->setEntityLabelInPlural('Absenzen')
            ->setSearchFields(['lessonStart', 'lessonEnd', 'id']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            DateField::new('date')->setLabel('Datum'),
            IntegerField::new('lessonStart')->setLabel('Beginn'),
            IntegerField::new('lessonEnd')->setLabel('Ende'),
            AssociationField::new('teacher')->setLabel('Lehrkraft')
                ->setRequired(false),
            AssociationField::new('studyGroup')->setLabel('Lerngruppe')
                ->setRequired(false),
            AssociationField::new('room')->setLabel('Raum')
                ->setRequired(false)
        ];
    }
}
