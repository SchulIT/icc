<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/settings')]
#[IsGranted('ROLE_ADMIN')]
class SettingsController extends AbstractController {
    #[Route(path: '', name: 'admin_settings')]
    public function index(): Response {
        return $this->redirectToRoute('admin_settings_general');
    }

}