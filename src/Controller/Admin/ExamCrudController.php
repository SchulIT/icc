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
        $description = TextField::new('description');
        $tuitions = AssociationField::new('tuitions');
        $students = AssociationField::new('students');
        $tuitionTeachersCanEditExam = Field::new('tuitionTeachersCanEditExam');
        $id = IntegerField::new('id', 'ID');
        $uuid = Field::new('uuid');
        $supervisions = AssociationField::new('supervisions');
        $room = AssociationField::new('room');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$externalId, $date, $lessonStart, $lessonEnd, $description, $tuitionTeachersCanEditExam, $id];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$externalId, $date, $lessonStart, $lessonEnd, $description, $tuitionTeachersCanEditExam, $id, $uuid, $tuitions, $students, $supervisions, $room];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$externalId, $date, $lessonStart, $lessonEnd, $description, $tuitions, $students];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$externalId, $date, $lessonStart, $lessonEnd, $description, $tuitions, $students];
        }
    }
}
