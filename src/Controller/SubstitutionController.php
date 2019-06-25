<?php

namespace App\Controller;

use App\Entity\MessageScope;
use Symfony\Component\Routing\Annotation\Route;

class SubstitutionController extends AbstractControllerWithMessages {


    /**
     * @Route("/substitutions", name="substitutions")
     */
    public function index() {

    }

    protected function getMessageScope(): MessageScope {
        return MessageScope::Substitutions();
    }
}