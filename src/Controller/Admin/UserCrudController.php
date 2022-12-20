<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\EnumField;
use App\Entity\User;
use App\Entity\UserType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

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
        $userType = EnumField::new('userType')->setFormType(EnumType::class)->setFormTypeOption('class', UserType::class);
        $teacher = AssociationField::new('teacher')->hideOnIndex();
        $students = AssociationField::new('students')->hideOnIndex();
        $idpId = TextField::new('idpId');
        $roles = ArrayField::new('roles')->hideOnIndex();
        $isSubstitutionNotificationsEnabled = Field::new('isSubstitutionNotificationsEnabled')->hideOnIndex();
        $isExamNotificationsEnabled = Field::new('isExamNotificationsEnabled')->hideOnIndex();
        $isMessageNotificationsEnabled = Field::new('isMessageNotificationsEnabled')->hideOnIndex();
        $isEmailNotificationsEnabled = Field::new('isEmailNotificationsEnabled')->hideOnIndex();
        $id = IntegerField::new('id', 'ID')->hideOnForm();

        return [$id, $idpId, $username, $firstname, $lastname, $email, $roles, $userType, $isSubstitutionNotificationsEnabled, $isExamNotificationsEnabled, $isMessageNotificationsEnabled, $isEmailNotificationsEnabled, $teacher, $students];
    }
}
