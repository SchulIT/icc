<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\EnumField;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FervoEnumBundle\Generated\Form\UserTypeType;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('User')
            ->setEntityLabelInPlural('User')
            ->setSearchFields(['idpId', 'username', 'firstname', 'lastname', 'email', 'roles', 'userType', 'data', 'id', 'uuid']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable('new');
    }

    public function configureFields(string $pageName): iterable
    {
        $username = TextField::new('username');
        $firstname = TextField::new('firstname');
        $lastname = TextField::new('lastname');
        $email = TextField::new('email');
        $userType = EnumField::new('userType')->setFormType(UserTypeType::class);
        $teacher = AssociationField::new('teacher');
        $students = AssociationField::new('students');
        $idpId = TextField::new('idpId');
        $roles = TextField::new('roles');
        $isSubstitutionNotificationsEnabled = Field::new('isSubstitutionNotificationsEnabled');
        $isExamNotificationsEnabled = Field::new('isExamNotificationsEnabled');
        $isMessageNotificationsEnabled = Field::new('isMessageNotificationsEnabled');
        $isEmailNotificationsEnabled = Field::new('isEmailNotificationsEnabled');
        $data = TextField::new('data');
        $id = IntegerField::new('id', 'ID');
        $uuid = Field::new('uuid');
        $dismissedMessages = AssociationField::new('dismissedMessages');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$username, $firstname, $lastname, $email, $userType, $teacher, $students, $idpId];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$idpId, $username, $firstname, $lastname, $email, $roles, $userType, $isSubstitutionNotificationsEnabled, $isExamNotificationsEnabled, $isMessageNotificationsEnabled, $isEmailNotificationsEnabled, $data, $id, $uuid, $teacher, $students, $dismissedMessages];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$username, $firstname, $lastname, $email, $userType, $teacher, $students];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$username, $firstname, $lastname, $email, $userType, $teacher, $students];
        }
    }
}
