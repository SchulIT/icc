<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Repository\TimetableWeekRepositoryInterface;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
#[Route(path: '/admin/timetable')]
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