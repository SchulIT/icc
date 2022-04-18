<?php

namespace App\Controller\Admin;

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
        $from = TextareaField::new('from')->setTemplatePath('admin/ea/date_lesson.html.twig');
        $until = TextareaField::new('until')->setTemplatePath('admin/ea/date_lesson.html.twig');
        $reason = Field::new('reason');
        $email = TextField::new('email');
        $phone = TextField::new('phone');
        $orderedBy = TextField::new('orderedBy');
        $message = TextareaField::new('message');
        $createdAt = DateTimeField::new('createdAt');
        $id = IntegerField::new('id', 'ID');
        $uuid = Field::new('uuid')->setTemplatePath('admin/ea/uuid.html.twig');
        $fromDate = DateField::new('from.date');
        $fromLesson = IntegerField::new('from.lesson');
        $untilDate = DateField::new('until.date');
        $untilLesson = IntegerField::new('until.lesson');
        $createdBy = AssociationField::new('createdBy');
        $attachments = AssociationField::new('attachments');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $uuid, $student, $from, $until];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$reason, $email, $phone, $orderedBy, $message, $createdAt, $id, $uuid, $fromDate, $fromLesson, $untilDate, $untilLesson, $student, $createdBy, $attachments];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$student, $from, $until, $reason, $email, $phone, $orderedBy, $message];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$student, $from, $until, $reason, $email, $phone, $orderedBy, $message];
        }
    }
}
