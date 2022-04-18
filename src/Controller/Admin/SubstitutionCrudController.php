<?php

namespace App\Controller\Admin;

use App\Entity\Substitution;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SubstitutionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Substitution::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Substitution')
            ->setEntityLabelInPlural('Substitution')
            ->setSearchFields(['externalId', 'lessonStart', 'lessonEnd', 'type', 'subject', 'replacementSubject', 'roomName', 'replacementRoomName', 'remark', 'id', 'uuid']);
    }

    public function configureFields(string $pageName): iterable
    {
        $externalId = TextField::new('externalId');
        $date = DateField::new('date');
        $lessonStart = IntegerField::new('lessonStart');
        $lessonEnd = IntegerField::new('lessonEnd');
        $startsBefore = Field::new('startsBefore');
        $type = TextField::new('type');
        $subject = TextField::new('subject');
        $replacementSubject = TextField::new('replacementSubject');
        $rooms = AssociationField::new('rooms');
        $replacementRooms = AssociationField::new('replacementRooms');
        $remark = TextField::new('remark');
        $teachers = AssociationField::new('teachers');
        $replacementTeachers = AssociationField::new('replacementTeachers');
        $studyGroups = AssociationField::new('studyGroups');
        $replacementStudyGroups = AssociationField::new('replacementStudyGroups');
        $replacementGrades = AssociationField::new('replacementGrades');
        $roomName = TextField::new('roomName');
        $replacementRoomName = TextField::new('replacementRoomName');
        $createdAt = DateTimeField::new('createdAt');
        $id = IntegerField::new('id', 'ID');
        $uuid = Field::new('uuid');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$externalId, $date, $lessonStart, $lessonEnd, $startsBefore, $type, $subject];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$externalId, $date, $lessonStart, $lessonEnd, $startsBefore, $type, $subject, $replacementSubject, $roomName, $replacementRoomName, $remark, $createdAt, $id, $uuid, $teachers, $replacementTeachers, $rooms, $replacementRooms, $studyGroups, $replacementStudyGroups, $replacementGrades];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$externalId, $date, $lessonStart, $lessonEnd, $startsBefore, $type, $subject, $replacementSubject, $rooms, $replacementRooms, $remark, $teachers, $replacementTeachers, $studyGroups, $replacementStudyGroups, $replacementGrades];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$externalId, $date, $lessonStart, $lessonEnd, $startsBefore, $type, $subject, $replacementSubject, $rooms, $replacementRooms, $remark, $teachers, $replacementTeachers, $studyGroups, $replacementStudyGroups, $replacementGrades];
        }
    }
}
