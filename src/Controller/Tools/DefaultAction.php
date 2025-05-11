<?php

namespace App\Controller\Tools;

use App\Menu\AdminToolsMenuBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tools', name: 'tools')]
class DefaultAction extends AbstractController {

    public function __construct(private readonly AdminToolsMenuBuilder $menuBuilder) { }


    public function __invoke(): RedirectResponse {
        $toolsMenu = $this->menuBuilder->toolsMenu([]);

        foreach($toolsMenu->getChildren() as $child) {
            if(!empty($child->getUri())) {
                return $this->redirect($child->getUri());
            }
        }

        throw new AccessDeniedHttpException();
    }
}