<?php

namespace App\Controller;

use App\Menu\AdminDataMenuBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin')]
class AdminController extends AbstractController {
    #[Route(path: '', name: 'admin')]
    public function index(AdminDataMenuBuilder $menuBuilder): Response {
        $adminMenu = $menuBuilder->dataMenu([]);

        if($adminMenu->hasChildren() === false) {
            throw new AccessDeniedHttpException();
        }

        return $this->redirect($adminMenu->getFirstChild()->getUri());
    }
}