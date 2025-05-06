<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\EnumField;
use App\Entity\User;
use App\Entity\UserType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
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

    public function configureFilters(Filters $filters): Filters {
        return $filters
            ->add('userType')
            ->add('teacher')
            ->add('students');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Benutzer')
            ->setEntityLabelInPlural('Benutzer')
            ->setSearchFields(['idpId', 'username', 'firstname', 'lastname', 'email', 'roles', 'userType', 'data', 'id', 'uuid']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable('new');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('idpId')
                ->setLabel('ID im Identity Provider')
                ->setHelp('Diese ID wird verwenden, um Benutzer vom IDP wiederzuerkennen, auch wenn sich Stammdaten (bspw. Benutzername) ändern.')
                ->setDisabled(true)
                ->hideOnIndex(),
            TextField::new('username')->setLabel('Benutzername'),
            TextField::new('firstname')->setLabel('Vorname'),
            TextField::new('lastname')->setLabel('Nachname'),
            EmailField::new('email')->setLabel('E-Mail-Adresse'),
            EnumField::new('userType')
                ->setLabel('Art')
                ->setFormType(EnumType::class)
                ->setFormTypeOption('class', UserType::class),
            ArrayField::new('roles')
                ->setLabel('Rollen')
                ->hideOnIndex(),
            AssociationField::new('teacher')
                ->setLabel('Lehrkraft')
                ->hideOnIndex()
                ->setFormTypeOption('required', false),
            AssociationField::new('students')
                ->hideOnIndex()
                ->setLabel('Lernende')
        ];
    }
}
