<?php

namespace App\Book\Grade\Export\XNM;

use App\Repository\TuitionGradeCategoryRepositoryInterface;
use App\Section\SectionResolverInterface;
use function Symfony\Component\String\u;

class ConfigurationGuesser {

    public function __construct(private readonly TuitionGradeCategoryRepositoryInterface $gradeCategoryRepository, private readonly SectionResolverInterface $sectionResolver) {

    }

    public function guess(): Configuration {
        $configuration = new Configuration();

        $categories = $this->gradeCategoryRepository->findAll();

        foreach($categories as $category) {
            $name = u($category->getDisplayName());

            if($name->containsAny('Zeugnis')) {
                $configuration->notenKategorie = $category;
            }
        }

        $configuration->section = $this->sectionResolver->getCurrentSection();
        return $configuration;
    }
}