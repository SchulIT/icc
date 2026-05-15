<?php

declare(strict_types=1);

namespace App\Framework\Feature\Twig;

use App\Framework\Feature\Feature;
use App\Framework\Feature\FeatureManager;
use Twig\Attribute\AsTwigFunction;
use Twig\Attribute\AsTwigFilter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FeatureExtension extends AbstractExtension {

    public function __construct(
        private readonly FeatureManager $featureManager
    ) {

    }

    public function getFunctions(): array {
        return [
            new TwigFunction('feature_enabled', [ $this, 'isFeatureEnabled' ])
        ];
    }

    #[AsTwigFunction('is_enabled')]
    public function isFeatureEnabled(string $feature): bool {
        return $this->featureManager->isFeatureEnabled(
            Feature::from($feature)
        );
    }
}