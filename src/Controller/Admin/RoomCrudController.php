<?php

namespace App\Controller\Admin;

use App\Entity\Room;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
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

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Room')
            ->setEntityLabelInPlural('Room')
            ->setSearchFields(['name', 'description', 'id', 'uuid', 'externalId', 'capacity']);
    }

    public function configureFields(string $pageName): iterable
    {
        $externalId = TextField::new('externalId');
        $name = TextField::new('name');
        $description = TextareaField::new('description');
        $isReservationEnabled = Field::new('isReservationEnabled');
        $capacity = IntegerField::new('capacity');
        $id = IntegerField::new('id', 'ID');
        $uuid = Field::new('uuid');
        $type = AssociationField::new('type');
        $tags = AssociationField::new('tags');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$name, $isReservationEnabled, $id, $externalId, $capacity, $type, $tags];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$name, $description, $isReservationEnabled, $id, $uuid, $externalId, $capacity, $type, $tags];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$externalId, $name, $description, $isReservationEnabled, $capacity];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$externalId, $name, $description, $isReservationEnabled, $capacity];
        }
    }
}
