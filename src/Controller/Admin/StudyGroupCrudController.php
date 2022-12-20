<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\EnumField;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class StudyGroupCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return StudyGroup::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('StudyGroup')
            ->setEntityLabelInPlural('StudyGroup')
            ->setSearchFields(['externalId', 'name', 'type', 'id', 'uuid']);
    }

    public function configureFields(string $pageName): iterable
    {
        $externalId = TextField::new('externalId');
        $name = TextField::new('name');
        $type = EnumField::new('type')
            ->setFormType(EnumType::class)
            ->setFormTypeOption('class', StudyGroupType::class);
        $grades = AssociationField::new('grades');
        $tuitions = AssociationField::new('tuitions')->hideOnIndex();
        $section = AssociationField::new('section');
        $id = IntegerField::new('id', 'ID')->hideOnForm();

        return [$id, $externalId, $name, $type, $grades, $tuitions, $section];
    }
}
