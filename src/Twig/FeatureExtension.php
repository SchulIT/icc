<?php

declare(strict_types=1);

namespace App\Twig;

use App\Feature\Feature;
use App\Feature\FeatureManager;
use Twig\Attribute\AsTwigFunction;
use Twig\Attribute\AsTwigFilter;

readonly class FeatureExtension {

    public function __construct(
        private FeatureManager $featureManager
    ) {

    }

    #[AsTwigFunction('is_enabled')]
    public function isFeatureEnabled(string $feature): bool {
        return $this->featureManager->isFeatureEnabled(
            Feature::from($feature)
        );
    }
}