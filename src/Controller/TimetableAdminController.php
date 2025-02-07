<?php

namespace App\Controller;

use App\Repository\TimetableWeekRepositoryInterface;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/timetable')]
#[IsGranted('ROLE_ADMIN')]
class TimetableAdminController extends AbstractController {

    public function __construct(private TimetableWeekRepositoryInterface $weekRepository, RefererHelper $refererHelper) {
        parent::__construct($refererHelper);
    }

    #[Route(path: '', name: 'admin_timetable')]
    public function index(): Response {
        return $this->render('admin/timetable/index.html.twig', [
            'weeks' => $this->weekRepository->findAll()
        ]);
    }

}