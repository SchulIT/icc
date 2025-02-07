<?php

namespace App\Feature;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class DisableControllerWhenFeatureIsNotEnabledSubscriber implements EventSubscriberInterface {

    public function __construct(private FeatureManager $featureManager) {

    }

    public function onKernelControllerArguments(ControllerArgumentsEvent $event): void {
        /** @var IsFeatureEnabled[] $attributes */
        $attributes = $event->getAttributes(IsFeatureEnabled::class);

        if(empty($attributes)) {
            // Attribute not present
            return;
        }

        foreach($attributes as $attribute) {
            if($this->featureManager->isFeatureEnabled($attribute->feature) !== true) {
                throw new NotFoundHttpException('Feature ' . $attribute->feature->value . ' ist deaktiviert.');
            }
        }
    }

    public static function getSubscribedEvents(): array {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => 'onKernelControllerArguments'
        ];
    }


}