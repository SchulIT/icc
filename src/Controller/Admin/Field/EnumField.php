<?php

namespace App\Controller\Admin\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use MyCLabs\Enum\Enum;

class EnumField implements FieldInterface {

    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null) {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplatePath('admin/ea/enum.html.twig');
    }
}