<?php

namespace App\Controller\Admin;

use App\Entity\Substitution;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SubstitutionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Substitution::class;
    }

    public function configureFilters(Filters $filters): Filters {
        return $filters
            ->add('date')
            ->add('type')
            ->add('subject')
            ->add('studyGroups')
            ->add('replacementStudyGroups')
            ->add('teachers')
            ->add('replacementTeachers')
            ->add('rooms')
            ->add('replacementRooms')
            ->add('replacementGrades')
            ->add('remark');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Vertretung')
            ->setEntityLabelInPlural('Vertretungen')
            ->setSearchFields(['externalId', 'lessonStart', 'lessonEnd', 'type', 'subject', 'replacementSubject', 'roomName', 'replacementRoomName', 'remark', 'id', 'uuid']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('externalId')->setLabel('Externe ID'),
            DateField::new('date')->setLabel('Datum'),
            IntegerField::new('lessonStart')->setLabel('Beginn'),
            IntegerField::new('lessonEnd')->setLabel('Ende'),
            BooleanField::new('startsBefore')
                ->setRequired(false)
                ->setLabel('startet vor der Stunde')
                ->setHelp('Gibt an, ob diese Vertretung vor der angegeben Stunde stattfindet (bspw. bei Pausenaufsichten).'),
            TextField::new('type')->setLabel('Art'),
            TextField::new('subject')->setLabel('Fach'),
            TextField::new('replacementSubject')
                ->setLabel('Vertretungsfach')
                ->hideOnIndex(),
            AssociationField::new('rooms')
                ->setLabel('Räume')
                ->hideOnIndex(),
            AssociationField::new('replacementRooms')
                ->setLabel('Vertretungsraum')
                ->hideOnIndex(),
            AssociationField::new('teachers')
                ->setLabel('Lehrkräfte')
                ->hideOnIndex(),
            AssociationField::new('replacementTeachers')
                ->setLabel('Vertretungslehrkräfte')
                ->hideOnIndex(),
            AssociationField::new('studyGroups')
                ->setLabel('Lerngruppen')
                ->hideOnIndex(),
            AssociationField::new('replacementStudyGroups')
                ->setLabel('Vertretungslerngruppen')
                ->hideOnIndex(),
            AssociationField::new('replacementGrades')
                ->setLabel('Vertretungsklasse')
                ->hideOnIndex(),
            TextField::new('roomName')
                ->setLabel('Raumname')
                ->hideOnIndex(),
            TextField::new('replacementRoomName')
                ->setLabel('Vertretungsraumname')
                ->hideOnIndex(),
            TextField::new('remark')
                ->setLabel('Bemerkung')
                ->hideOnIndex()
        ];
    }
}
