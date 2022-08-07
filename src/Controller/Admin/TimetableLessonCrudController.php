<?php

namespace App\Controller\Admin;

use App\Entity\TimetableLesson;
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


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('externalId'),
            DateField::new('date'),
            IntegerField::new('lessonStart'),
            IntegerField::new('lessonEnd'),
            AssociationField::new('tuition'),
            AssociationField::new('room'),
            TextField::new('location'),
            AssociationField::new('subject'),
            TextField::new('subjectName'),
            AssociationField::new('teachers'),
            AssociationField::new('grades')
        ];
    }

}
