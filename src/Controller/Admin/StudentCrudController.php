<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\EnumField;
use App\Entity\Student;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FervoEnumBundle\Generated\Form\GenderType;

class StudentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Student::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Student')
            ->setEntityLabelInPlural('Student')
            ->setSearchFields(['externalId', 'uniqueIdentifier', 'firstname', 'lastname', 'gender', 'email', 'status', 'id', 'uuid']);
    }

    public function configureFields(string $pageName): iterable
    {
        $externalId = TextField::new('externalId');
        $firstname = TextField::new('firstname');
        $lastname = TextField::new('lastname');
        $gender = EnumField::new('gender')->setFormType(GenderType::class);
        $email = TextField::new('email');
        $status = TextField::new('status');
        $birthday = DateField::new('birthday');
        $approvedPrivacyCategories = AssociationField::new('approvedPrivacyCategories');
        $uniqueIdentifier = TextField::new('uniqueIdentifier');
        $id = IntegerField::new('id', 'ID');
        $uuid = Field::new('uuid');
        $gradeMemberships = AssociationField::new('gradeMemberships');
        $studyGroupMemberships = AssociationField::new('studyGroupMemberships');
        $sections = AssociationField::new('sections');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$externalId, $firstname, $lastname, $gender, $email, $status, $birthday, $approvedPrivacyCategories];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$externalId, $uniqueIdentifier, $firstname, $lastname, $gender, $email, $status, $birthday, $id, $uuid, $gradeMemberships, $studyGroupMemberships, $approvedPrivacyCategories, $sections];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$externalId, $firstname, $lastname, $gender, $email, $status, $birthday, $approvedPrivacyCategories];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$externalId, $firstname, $lastname, $gender, $email, $status, $birthday, $approvedPrivacyCategories];
        }
    }
}
