<?php

namespace App\Controller\Admin;

use App\Entity\Exam;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ExamCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Exam::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Exam')
            ->setEntityLabelInPlural('Exam')
            ->setSearchFields(['externalId', 'lessonStart', 'lessonEnd', 'description', 'id', 'uuid']);
    }

    public function configureFields(string $pageName): iterable
    {
        $externalId = TextField::new('externalId');
        $date = DateField::new('date');
        $lessonStart = IntegerField::new('lessonStart');
        $lessonEnd = IntegerField::new('lessonEnd');
        $description = TextField::new('description')->hideOnIndex();
        $tuitions = AssociationField::new('tuitions');
        $students = AssociationField::new('students');
        $tuitionTeachersCanEditExam = Field::new('tuitionTeachersCanEditExam')->hideOnIndex();
        $id = IntegerField::new('id', 'ID')->hideOnForm();
        $room = AssociationField::new('room')->hideOnIndex();

        return [$id, $externalId, $date, $lessonStart, $lessonEnd, $description, $tuitionTeachersCanEditExam, $tuitions, $students, $room];
    }
}
