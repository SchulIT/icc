<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\EmailType;

class EmailCollectionEntryType extends EmailType {
    public function getBlockPrefix(): string {
        return 'text_collection_entry';
    }
}