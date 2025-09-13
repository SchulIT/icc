<?php

namespace App\Feature;

use App\Settings\FeatureSettings;
use ReflectionClassConstant;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

readonly class FeatureManager {

    /**
     * @param FeatureSettings $settings
     * @param RequirementCheckerInterface[] $requirementsChecker
     */
    public function __construct(private FeatureSettings $settings,
                                #[AutowireIterator('app.features.requirements_checker')] private iterable $requirementsChecker) {

    }

    public function isFeatureEnabled(Feature $feature): bool {
        $classConstant = new ReflectionClassConstant(get_class($feature), $feature->name);
        $attributes = $classConstant->getAttributes(Requires::class);
        $attribute = $attributes[0] ?? null;
        $attribute = $attribute?->newInstance();

        foreach($this->requirementsChecker as $checker) {
            if($checker->supports($attribute) && $checker->isRequirementFulfilled($attribute, $this)) {
                return $this->settings->isFeatureEnabled($feature);
            }
        }

        return false;
    }
}