<?php

namespace App\Controller\Admin;

use App\Entity\TimetableLesson;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TimetableLessonCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TimetableLesson::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('TimetableLesson')
            ->setEntityLabelInPlural('TimetableLesson')
            ->setSearchFields(['externalId', 'day', 'lesson', 'location', 'subjectName', 'id', 'uuid']);
    }

    public function configureFields(string $pageName): iterable
    {
        $externalId = TextField::new('externalId');
        $period = AssociationField::new('period');
        $week = AssociationField::new('week');
        $day = IntegerField::new('day');
        $lesson = IntegerField::new('lesson');
        $isDoubleLesson = Field::new('isDoubleLesson');
        $tuition = AssociationField::new('tuition');
        $room = AssociationField::new('room');
        $subject = AssociationField::new('subject');
        $location = TextField::new('location');
        $subjectName = TextField::new('subjectName');
        $id = IntegerField::new('id', 'ID');
        $uuid = Field::new('uuid');
        $teachers = AssociationField::new('teachers');
        $grades = AssociationField::new('grades');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$externalId, $day, $lesson, $isDoubleLesson, $location, $subjectName, $id];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$externalId, $day, $lesson, $isDoubleLesson, $location, $subjectName, $id, $uuid, $period, $week, $tuition, $room, $subject, $teachers, $grades];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$externalId, $period, $week, $day, $lesson, $isDoubleLesson, $tuition, $room, $subject, $location];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$externalId, $period, $week, $day, $lesson, $isDoubleLesson, $tuition, $room, $subject, $location];
        }
    }
}
