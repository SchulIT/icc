<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/settings')]
#[Security("is_granted('ROLE_ADMIN')")]
class SettingsController extends AbstractController {
    #[Route(path: '', name: 'admin_settings')]
    public function index(): Response {
        return $this->redirectToRoute('admin_settings_general');
    }

}