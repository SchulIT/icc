<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Menu\Builder;
use Symfony\Component\Routing\Annotation\Route;

class SystemController extends AbstractController {

    #[Route(path: '/admin/system', name: 'system')]
    public function index(Builder $menuBuilder): Response {
        $adminMenu = $menuBuilder->systemMenu([]);

        return $this->render('admin/system.html.twig', [
            'menu' => $adminMenu->getChildren()
        ]);
    }
}