<?php

namespace App\Book\Grade\Export\ZP10;

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

            if(!$name->containsAny('ZP10')) {
                continue;
            }

            if($name->containsAny('muendl') || $name->containsAny('mündl')) {
                $configuration->muendlich = $category;
            } else if($name->containsAny('schrift')) {
                $configuration->schriftlich = $category;
            } else if($name->containsAny('Vornote')) {
                $configuration->vornote = $category;
            } else if($name->containsAny('End') || $name->containsAny('Abschluss')) {
                $configuration->abschlussNote = $category;
            }
        }

        $configuration->section = $this->sectionResolver->getCurrentSection();

        return $configuration;
    }
}