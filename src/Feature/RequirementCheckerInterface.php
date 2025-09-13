<?php

namespace App\Feature;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.features.requirements_checker')]
interface RequirementCheckerInterface {
    public function supports(Requires|null $requires): bool;

    public function isRequirementFulfilled(Requires|null $requires, FeatureManager $featureManager): bool;
}