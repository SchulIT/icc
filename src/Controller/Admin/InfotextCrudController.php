<?php

namespace App\Controller\Admin;

use App\Entity\Infotext;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class InfotextCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Infotext::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Infotext')
            ->setEntityLabelInPlural('Infotext')
            ->setSearchFields(['content', 'id', 'uuid']);
    }

    public function configureFields(string $pageName): iterable
    {
        $date = DateField::new('date');
        $content = TextareaField::new('content');
        $id = IntegerField::new('id', 'ID');
        $uuid = Field::new('uuid');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$date, $id];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$date, $content, $id, $uuid];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$date, $content];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$date, $content];
        }
    }
}
