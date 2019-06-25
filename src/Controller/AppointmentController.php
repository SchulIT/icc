<?php

namespace App\Controller;

use App\Entity\MessageScope;
use Symfony\Component\Routing\Annotation\Route;

class AppointmentController extends AbstractControllerWithMessages {

    /**
     * @Route("/appointments", name="appointments")
     */
    public function index() {

    }

    protected function getMessageScope(): MessageScope {
        return MessageScope::Appointments();
    }
}