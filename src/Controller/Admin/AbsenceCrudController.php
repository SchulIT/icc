<?php

namespace App\Controller\Admin;

use App\Entity\Absence;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
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

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Absence')
            ->setEntityLabelInPlural('Absence')
            ->setSearchFields(['lessonStart', 'lessonEnd', 'id']);
    }

    public function configureFields(string $pageName): iterable
    {
        $date = DateField::new('date');
        $lessonStart = IntegerField::new('lessonStart');
        $lessonEnd = IntegerField::new('lessonEnd');
        $teacher = AssociationField::new('teacher');
        $studyGroup = AssociationField::new('studyGroup');
        $id = IntegerField::new('id', 'ID')->hideOnForm();
        $room = AssociationField::new('room');

        return [$id, $date, $lessonStart, $lessonEnd, $teacher, $studyGroup, $room];
    }
}
