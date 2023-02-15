<?php

namespace App\Controller\Admin;

use App\Entity\FreeTimespan;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class FreeTimespanCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FreeTimespan::class;
    }

    public function configureFilters(Filters $filters): Filters {
        return $filters
            ->add('date');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Unterrichtsfreie Zeit')
            ->setEntityLabelInPlural('Unterrichtsfreie Zeiten')
            ->setSearchFields(['start', 'end', 'id']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            DateField::new('date')->setLabel('Datum'),
            IntegerField::new('start')->setLabel('Beginn'),
            IntegerField::new('end')->setLabel('Ende')
        ];
    }
}
