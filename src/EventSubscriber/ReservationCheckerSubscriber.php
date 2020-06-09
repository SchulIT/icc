<?php

namespace App\EventSubscriber;

use App\Entity\RoomReservation;
use App\Entity\Substitution;
use App\Event\SubstitutionImportEvent;
use App\Repository\RoomReservationRepositoryInterface;
use DateTime;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class ReservationCheckerSubscriber implements EventSubscriberInterface {

    private $validator;
    private $reservationRepository;

    private $appName;
    private $sender;

    private $mailer;
    private $twig;
    private $translator;

    public function __construct($appName, string $sender, ValidatorInterface $validator, RoomReservationRepositoryInterface $reservationRepository, Swift_Mailer $mailer, Environment $twig, TranslatorInterface $translator) {
        $this->appName = $appName;
        $this->sender = $sender;

        $this->validator = $validator;
        $this->reservationRepository = $reservationRepository;

        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->translator = $translator;
    }

    public function onSubstitutionImportEvent(SubstitutionImportEvent $event): void {
        $substitutions = array_merge($event->getAdded(), $event->getUpdated());
        /** @var DateTime $start */
        $start = null;

        /** @var DateTime $end */
        $end = null;

        /** @var Substitution $substitution */
        foreach($substitutions as $substitution) {
            if($start === null || $substitution->getDate() < $start) {
                $start = clone $substitution->getDate();
            }

            if($end === null || $substitution->getDate() > $end) {
                $end = clone $substitution->getDate();
            }
        }

        if($start === null || $end === null) {
            return;
        }

        while($start <= $end) {
            $reservations = $this->reservationRepository->findAllByDate($start);

            foreach($reservations as $reservation) {
                $violations = $this->validator->validate($reservation);

                if(count($violations) > 0) {
                    $this->sendViolationsEmail($reservation, $violations);
                }
            }

            $start->modify('+1 day');
        }
    }

    private function sendViolationsEmail(RoomReservation $reservation, ConstraintViolationListInterface $violationList): void {
        $content = $this->twig->render('email/reservation.html.twig', [
            'reservation' => $reservation,
            'validation_errors' => $violationList
        ]);

        $message = (new Swift_Message())
            ->setSubject($this->translator->trans('reservation.title', [],'email'))
            ->setFrom([$this->sender], $this->appName)
            ->setSender($this->sender, $this->appName)
            ->setBody($content)
            ->setTo([ $reservation->getTeacher()->getEmail() ]);

        $this->mailer->send($message);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents() {
        return [
            SubstitutionImportEvent::class => 'onSubstitutionImportEvent'
        ];
    }
}