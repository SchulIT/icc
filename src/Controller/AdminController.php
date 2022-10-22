<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Menu\Builder;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin')]
class AdminController extends AbstractController {
    #[Route(path: '', name: 'admin')]
    public function index(Builder $menuBuilder): Response {
        $adminMenu = $menuBuilder->adminMenu([]);

        if($adminMenu->hasChildren() === false || !isset($adminMenu['admin']) || $adminMenu['admin']->hasChildren() === false) {
            throw new AccessDeniedHttpException();
        }

        return $this->render('admin/index.html.twig', [
            'menu' => $adminMenu->getChildren()
        ]);
    }
}