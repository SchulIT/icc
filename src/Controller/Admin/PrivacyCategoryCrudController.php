<?php

namespace App\Controller\Admin;

use App\Entity\PrivacyCategory;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PrivacyCategoryCrudController extends AbstractCrudController {

    public static function getEntityFqcn(): string {
        return PrivacyCategory::class;
    }

    public function configureCrud(Crud $crud): Crud {
        return $crud
            ->setEntityLabelInSingular('Datenschutzkategorie')
            ->setEntityLabelInPlural('Datenschutzkategorien')
            ->setSearchFields(['externalId', 'label', 'description']);
    }

    public function configureFields(string $pageName): iterable {
        return [
            TextField::new('externalId')->setLabel('Externe ID'),
            TextField::new('label')->setLabel('Name'),
            TextareaField::new('description')->setLabel('Beschreibung')
        ];
    }
}