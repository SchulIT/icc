<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\EnumField;
use App\Entity\SickNote;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FervoEnumBundle\Generated\Form\SickNoteReasonType;

class SickNoteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SickNote::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('SickNote')
            ->setEntityLabelInPlural('SickNote')
            ->setSearchFields(['reason', 'email', 'phone', 'orderedBy', 'message', 'id', 'uuid', 'from.lesson', 'until.lesson']);
    }

    public function configureFields(string $pageName): iterable
    {
        $student = AssociationField::new('student');
        $reason = EnumField::new('reason')->setFormType(SickNoteReasonType::class);
        $email = TextField::new('email')->hideOnIndex();
        $phone = TextField::new('phone')->hideOnIndex();
        $orderedBy = TextField::new('orderedBy')->hideOnIndex();
        $message = TextareaField::new('message')->hideOnIndex();
        $createdAt = DateTimeField::new('createdAt')->hideOnIndex();
        $id = IntegerField::new('id', 'ID')->hideOnForm();
        $fromDate = DateField::new('from.date');
        $fromLesson = IntegerField::new('from.lesson');
        $untilDate = DateField::new('until.date');
        $untilLesson = IntegerField::new('until.lesson');
        $createdBy = AssociationField::new('createdBy')->hideOnIndex();

        return [$id, $fromDate, $fromLesson, $untilDate, $untilLesson, $reason, $email, $phone, $orderedBy, $message, $createdAt, $student, $createdBy];
    }
}
