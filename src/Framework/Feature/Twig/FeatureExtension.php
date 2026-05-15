<?php

declare(strict_types=1);

namespace App\Framework\Feature\Twig;

use App\Framework\Feature\Feature;
use App\Framework\Feature\FeatureManager;
use Twig\Attribute\AsTwigFunction;

readonly class FeatureExtension {

    public function __construct(
        private FeatureManager $featureManager
    ) { }

    #[AsTwigFunction('feature_enabled')]
    public function isFeatureEnabled(string $feature): bool {
        return $this->featureManager->isFeatureEnabled(
            Feature::from($feature)
        );
    }
}