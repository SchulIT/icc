<?php

namespace App\Form;

use App\Notification\Delivery\DeliverStrategyType;
use App\Notification\NotificationDeliveryTarget;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DeliveryOptionCompoundType extends AbstractType {

    public function __construct(private readonly TranslatorInterface $translator, #[Autowire('%env(PUSHOVER_TOKEN)%')] private readonly ?string $pushoverToken) {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        foreach(NotificationDeliveryTarget::cases() as $target) {
            $builder
                ->add($target->value, EnumType::class, [
                    'class' => DeliverStrategyType::class,
                    'required' => true,
                    'multiple' => false,
                    'label' => $target->trans($this->translator),
                    'disabled' => $target === NotificationDeliveryTarget::Pushover && empty($this->pushoverToken)
                ]);
        }
    }
}