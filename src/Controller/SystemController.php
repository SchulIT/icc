<?php

namespace App\Controller;

use App\Menu\SystemMenuBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SystemController extends AbstractController {

    #[Route(path: '/admin/system', name: 'system')]
    public function index(SystemMenuBuilder $menuBuilder): Response {
        $adminMenu = $menuBuilder->systemMenu([]);

        return $this->render('admin/system.html.twig', [
            'menu' => $adminMenu->getChildren()
        ]);
    }
}