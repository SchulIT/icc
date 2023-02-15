<?php

namespace App\Controller\Admin;

use App\Entity\Exam;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ExamCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Exam::class;
    }

    public function configureFilters(Filters $filters): Filters {
        return $filters
            ->add('date')
            ->add('tuitions')
            ->add('students')
            ->add('room');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Klausur')
            ->setEntityLabelInPlural('Klausuren')
            ->setSearchFields(['externalId', 'lessonStart', 'lessonEnd', 'description', 'id', 'uuid']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('externalId')->setLabel('Externe ID'),
            DateField::new('date')->setLabel('Datum'),
            IntegerField::new('lessonStart')->setLabel('Beginn'),
            IntegerField::new('lessonEnd')->setLabel('Ende'),
            TextareaField::new('description')->setLabel('Beschreibung'),
            AssociationField::new('tuitions')->setLabel('Unterrichte'),
            AssociationField::new('students')->setLabel('Lernende'),
            AssociationField::new('room')->setLabel('Raum'),
            BooleanField::new('tuitionTeachersCanEditExam')->setLabel('Lehrkräfte können Klausur bearbeiten')
        ];
    }
}
