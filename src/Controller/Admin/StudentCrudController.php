<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\EnumField;
use App\Entity\Gender;
use App\Entity\Student;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

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
        $gender = EnumField::new('gender')
            ->setFormType(EnumType::class)
            ->setFormTypeOption('class', Gender::class);
        $email = TextField::new('email');
        $status = TextField::new('status');
        $birthday = DateField::new('birthday')->hideOnIndex();
        $approvedPrivacyCategories = AssociationField::new('approvedPrivacyCategories')->hideOnIndex();
        $uniqueIdentifier = TextField::new('uniqueIdentifier')->hideOnIndex();
        $id = IntegerField::new('id', 'ID')->hideOnForm();
        $sections = AssociationField::new('sections')->hideOnIndex();

        return [$id, $externalId, $uniqueIdentifier, $firstname, $lastname, $gender, $email, $status, $birthday, $approvedPrivacyCategories, $sections];
    }
}
