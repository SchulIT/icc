<?php

namespace App\Controller\Admin;

use App\Entity\TimetableSupervision;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TimetableSupervisionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TimetableSupervision::class;
    }

    public function configureFilters(Filters $filters): Filters {
        return $filters
            ->add('date')
            ->add('teacher')
            ->add('location');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Aufsicht')
            ->setEntityLabelInPlural('Aufsichten')
            ->setSearchFields(['externalId', 'lessonStart', 'lessonEnd', 'type', 'subject', 'replacementSubject', 'roomName', 'replacementRoomName', 'remark', 'id', 'uuid']);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('externalId')->setLabel('Externe ID'),
            DateField::new('date')->setLabel('Datum'),
            IntegerField::new('lesson')->setLabel('Stunde'),
            BooleanField::new('isBefore')->setLabel('vor der Stunde'),
            AssociationField::new('teacher')->setLabel('Lehrkraft'),
            TextField::new('location')->setLabel('Ort')
        ];
    }

}
