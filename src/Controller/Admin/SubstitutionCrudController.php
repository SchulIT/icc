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
        $replacementSubject = TextField::new('replacementSubject')->hideOnIndex();
        $rooms = AssociationField::new('rooms')->hideOnIndex();
        $replacementRooms = AssociationField::new('replacementRooms')->hideOnIndex();
        $remark = TextField::new('remark')->hideOnIndex();
        $teachers = AssociationField::new('teachers')->hideOnIndex();
        $replacementTeachers = AssociationField::new('replacementTeachers')->hideOnIndex();
        $studyGroups = AssociationField::new('studyGroups')->hideOnIndex();
        $replacementStudyGroups = AssociationField::new('replacementStudyGroups')->hideOnIndex();
        $replacementGrades = AssociationField::new('replacementGrades')->hideOnIndex();
        $roomName = TextField::new('roomName')->hideOnIndex();
        $replacementRoomName = TextField::new('replacementRoomName')->hideOnIndex();
        $id = IntegerField::new('id', 'ID')->hideOnForm();

        return [$id, $externalId, $date, $lessonStart, $lessonEnd, $startsBefore, $type, $subject, $replacementSubject, $roomName, $replacementRoomName, $remark, $teachers, $replacementTeachers, $rooms, $replacementRooms, $studyGroups, $replacementStudyGroups, $replacementGrades];
    }
}
