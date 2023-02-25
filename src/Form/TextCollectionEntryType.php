<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class TextCollectionEntryType extends TextType {
    public function getBlockPrefix(): string {
        return 'text_collection_entry';
    }
}