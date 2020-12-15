<?php

namespace App\Controller;

use App\Menu\Builder;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController {

    /**
     * @Route("", name="admin")
     */
    public function index(Builder $menuBuilder) {
        $adminMenu = $menuBuilder->adminMenu([]);

        if($adminMenu->hasChildren() === false || !isset($adminMenu['admin']) || $adminMenu['admin']->hasChildren() === false) {
            throw new AccessDeniedHttpException();
        }

        return $this->render('admin/index.html.twig', [
            'menu' => $adminMenu->getChildren()
        ]);
    }
}