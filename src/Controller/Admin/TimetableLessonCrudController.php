<?php

namespace App\Controller\Admin;

use App\Entity\TimetableLesson;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TimetableLessonCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TimetableLesson::class;
    }

    public function configureFilters(Filters $filters): Filters {
        return $filters
            ->add('date')
            ->add('teachers')
            ->add('grades')
            ->add('tuition')
            ->add('subject')
            ->add('subjectName')
            ->add('room')
            ->add('location');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Stundenplanstunde')
            ->setEntityLabelInPlural('Stundenplanstunden')
            ->setSearchFields(['externalId', 'lessonStart', 'lessonEnd', 'type', 'subject', 'replacementSubject', 'roomName', 'replacementRoomName', 'remark', 'id', 'uuid']);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('externalId')->setLabel('Externe ID'),
            DateField::new('date')->setLabel('Datum'),
            IntegerField::new('lessonStart')->setLabel('Beginn'),
            IntegerField::new('lessonEnd')->setLabel('Ende'),
            AssociationField::new('tuition')->setLabel('Unterricht'),
            AssociationField::new('room')->setLabel('Raum'),
            TextField::new('location')->setLabel('Ort'),
            AssociationField::new('subject')->setLabel('Fach'),
            TextField::new('subjectName')->setLabel('Fachname'),
            AssociationField::new('teachers')->setLabel('Lehrkräfte'),
            AssociationField::new('grades')->setLabel('Klassen')
        ];
    }

}
