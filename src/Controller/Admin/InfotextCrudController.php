<?php

namespace App\Controller\Admin;

use App\Entity\Infotext;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class InfotextCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Infotext::class;
    }

    public function configureFilters(Filters $filters): Filters {
        return $filters->add('date');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Tagestext')
            ->setEntityLabelInPlural('Tagestexte')
            ->setSearchFields(['content', 'id', 'uuid']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            DateField::new('date')->setLabel('Datum'),
            TextareaField::new('content')->setLabel('Text')
        ];
    }
}
