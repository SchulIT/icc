<?php

namespace App\Controller\Admin;

use App\Entity\TimetableSupervision;
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


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('externalId'),
            DateField::new('date'),
            IntegerField::new('lesson'),
            BooleanField::new('isBefore'),
            AssociationField::new('teacher'),
            TextField::new('location')
        ];
    }

}
