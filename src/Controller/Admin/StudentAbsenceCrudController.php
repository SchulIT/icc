<?php

namespace App\Controller\Admin;

use App\Entity\StudentAbsence;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class StudentAbsenceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return StudentAbsence::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('StudentAbsence')
            ->setEntityLabelInPlural('StudentAbsences')
            ->setSearchFields(['type.name', 'email', 'phone', 'message', 'id', 'uuid', 'from.lesson', 'until.lesson']);
    }

    public function configureFields(string $pageName): iterable
    {
        $student = AssociationField::new('student');
        $type = AssociationField::new('type');
        $email = TextField::new('email')->hideOnIndex();
        $phone = TextField::new('phone')->hideOnIndex();
        $message = TextareaField::new('message')->hideOnIndex();
        $createdAt = DateTimeField::new('createdAt')->hideOnIndex();
        $id = IntegerField::new('id', 'ID')->hideOnForm();
        $fromDate = DateField::new('from.date');
        $fromLesson = IntegerField::new('from.lesson');
        $untilDate = DateField::new('until.date');
        $untilLesson = IntegerField::new('until.lesson');
        $createdBy = AssociationField::new('createdBy')->hideOnIndex();

        return [$id, $fromDate, $fromLesson, $untilDate, $untilLesson, $type, $email, $phone, $message, $createdAt, $student, $createdBy];
    }
}
