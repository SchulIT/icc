<?php

namespace App\Notification\Email;

use App\Converter\UserStringConverter;
use App\Event\AppointmentConfirmedEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

class AppointmentConfirmedStrategy implements EmailStrategyInterface {

    private $translator;
    private $converter;

    public function __construct(TranslatorInterface $translator, UserStringConverter $converter) {
        $this->translator = $translator;
        $this->converter = $converter;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function supports($objective): bool {
        return $objective instanceof AppointmentConfirmedEvent;
    }

    /**
     * @param AppointmentConfirmedEvent $objective
     * @return string|null
     */
    public function getReplyTo($objective): ?string {
        return $objective->getConfirmedBy()->getEmail();
    }

    /**
     * @param AppointmentConfirmedEvent $objective
     * @return array
     */
    public function getRecipients($objective): array {
        return [ $objective->getAppointment()->getCreatedBy() ];
    }

    /**
     * @inheritDoc
     */
    public function getSubject($objective): string {
        return $this->translator->trans('appointment.title', [], 'email');
    }

    /**
     * @param AppointmentConfirmedEvent $objective
     * @return string
     */
    public function getSender($objective): string {
        return $this->converter->convert($objective->getConfirmedBy(), false);
    }

    /**
     * @inheritDoc
     */
    public function getTemplate(): string {
        return 'email/appointment.html.twig';
    }
}