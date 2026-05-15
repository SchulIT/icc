<?php

namespace App\Substitution\Controller;

use App\Common\Entity\User;
use App\Framework\Controller\AbstractController;
use App\Framework\Http\Attribute\MapDateFromQuery;
use App\Overtime\OvertimeOverviewGenerator;
use App\Substitution\Voter\OvertimeVoter;
use App\Common\View\Filter\SectionFilter;
use App\Common\View\Filter\TeacherFilter;
use DateInterval;
use DateTime;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class OvertimeController extends AbstractController {

    #[Route('/substitutions/overtime', name: 'overtime')]
    public function __invoke(
        OvertimeOverviewGenerator $generator,
        SectionFilter $sectionFilter,
        TeacherFilter $teacherFilter,
        Request $request,
        DateHelper $dateHelper,
        #[CurrentUser] User $user,
        #[MapDateFromQuery] DateTime|null $start,
        #[MapDateFromQuery] DateTime|null $end
    ): Response {
        $this->denyAccessUnlessGranted(OvertimeVoter::View);

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher'), $sectionFilterView->getCurrentSection(), $user, true);

        if($start === null || $end === null) {
            $today = $dateHelper->getToday();
            $start = $today->setDate(intval($today->format('Y')), intval($today->format('m')), 1);
            $end = (clone $start)->add(new DateInterval('P1M'))->sub(new DateInterval('P1D'));
        }

        $overview = null;
        if($teacherFilterView->getCurrentTeacher() !== null) {
            $overview = $generator->generate($teacherFilterView->getCurrentTeacher(), $start, $end);
        }

        return $this->render('substitutions/overtime.html.twig', [
            'start' => $start,
            'end' => $end,
            'overview' => $overview,
            'teacherFilter' => $teacherFilterView,
            'sectionFilter' => $sectionFilterView,
            'months' => $this->getMonthsStartsAndEnds($sectionFilterView->getCurrentSection()->getStart(), $sectionFilterView->getCurrentSection()->getEnd()),
        ]);
    }

    private function getMonthsStartsAndEnds(DateTime $start, DateTime $end): array {
        $result = [ ];

        if($start > $end) {
            $tmp = $start;
            $start = $end;
            $end = $tmp;
        }

        $start = (new DateTime())->setDate(intval($start->format('Y')), intval($start->format('m')), 1);
        while($start < $end) {
            $next = (clone $start)->add(new DateInterval('P1M'));
            $result[] = [
                'start' => $start,
                'end' => (clone $next)->sub(new DateInterval('P1D')),
            ];

            $start = $next;
        }

        return $result;
    }
}