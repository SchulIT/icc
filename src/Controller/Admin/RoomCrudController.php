<?php

namespace App\Controller\Admin;

use App\Entity\Room;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RoomCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Room::class;
    }

    public function configureFilters(Filters $filters): Filters {
        return $filters
            ->add('isReservationEnabled');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Raum')
            ->setEntityLabelInPlural('Räume')
            ->setSearchFields(['name', 'description', 'id', 'uuid', 'externalId', 'capacity']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('externalId')->setLabel('Externe ID'),
            TextField::new('name')->setLabel('Name'),
            TextareaField::new('description')->setLabel('Beschreibung'),
            BooleanField::new('isReservationEnabled')->setLabel('Reservierung möglich'),
            IntegerField::new('capacity')->setLabel('Kapazität'),
            AssociationField::new('type')->setLabel('Art')
        ];
    }
}
