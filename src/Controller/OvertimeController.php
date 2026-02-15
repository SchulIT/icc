<?php

namespace App\Controller;

use App\Entity\User;
use App\Http\Attribute\MapDateFromQuery;
use App\Overtime\OvertimeOverviewGenerator;
use App\Security\Voter\OvertimeVoter;
use App\View\Filter\SectionFilter;
use App\View\Filter\TeacherFilter;
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
            $start = $today->setDate($today->format('Y'), $today->format('m'), 1);
            $end = (clone $start)->add(new DateInterval('P1M'))->sub(new DateInterval('P1D'));
        }

        $overview = null;
        if($start !== null && $end !== null && $teacherFilterView->getCurrentTeacher() !== null) {
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

        $start = (new DateTime())->setDate($start->format('Y'), $start->format('m'), 1);
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