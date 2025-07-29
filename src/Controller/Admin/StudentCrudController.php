<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\EnumField;
use App\Entity\Gender;
use App\Entity\Student;
use App\Form\StudentLearningManagementSystemInformationType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class StudentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Student::class;
    }

    public function configureFilters(Filters $filters): Filters {
        return $filters
            ->add('externalId')
            ->add('uuid')
            ->add('firstname')
            ->add('lastname')
            ->add('gender')
            ->add('status')
            ->add('sections');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Schülerin/Schüler')
            ->setEntityLabelInPlural('Schülerinnen und Schüler')
            ->setSearchFields(['externalId', 'firstname', 'lastname', 'gender', 'email', 'status', 'id', 'uuid']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('externalId')->setLabel('Externe ID'),
            TextField::new('firstname')->setLabel('Vorname'),
            TextField::new('lastname')->setLabel('Nachname'),
            ChoiceField::new('gender')
                ->setChoices(Gender::cases())
                ->setLabel('Geschlecht'),
            EmailField::new('email')->setLabel('E-Mail-Adresse'),
            TextField::new('status')->setLabel('Status'),
            DateField::new('birthday')->setLabel('Geburtstag')->hideOnIndex(),
            AssociationField::new('sections')
                ->setLabel('Abschnitte')
                ->setFormTypeOption('expanded', true)
                ->hideOnIndex(),
            AssociationField::new('approvedPrivacyCategories')
                ->setLabel('Zugestimmte Datenschutzkategorien')
                ->setFormTypeOption('expanded', true)
                ->hideOnIndex(),
            CollectionField::new('learningManagementSystems')
                ->setLabel('Lernplattformen')
                ->setEntryType(StudentLearningManagementSystemInformationType::class)
                ->hideOnIndex()
                ->allowAdd(true)
                ->allowDelete(true)
                ->setFormTypeOption('by_reference', false)
        ];
    }
}
