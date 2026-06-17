<?php

namespace App\Book\Controller;

use App\Book\Entity\ReportRemark;
use App\Book\Form\ReportRemarkType;
use App\Book\Grade\Export\ZP10\ConfigurationType;
use App\Book\Repository\ReportRemarkRepositoryInterface;
use App\Book\Settings\ReportRemarksSettings;
use App\Book\Voter\ReportRemarkVoter;
use App\Common\Entity\User;
use App\Common\Section\SectionResolverInterface;
use App\Common\View\Filter\SectionFilter;
use App\Framework\Controller\AbstractController;
use App\Framework\Feature\Feature;
use App\Framework\Feature\IsFeatureEnabled;
use App\Framework\Repository\PaginationQuery;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/book/report_remark')]
#[IsFeatureEnabled(Feature::Book)]
#[IsFeatureEnabled(Feature::GradeBook)]
class ReportRemarkController extends AbstractController {

    public const int ITEMS_PER_PAGE = 50;

    public function __construct(
        RefererHelper $redirectHelper,
        private readonly ReportRemarkRepositoryInterface $repository
    ) {
        parent::__construct($redirectHelper);
    }

    #[Route('', name: 'report_remarks')]
    public function index(
        SectionFilter $sectionFilter,
        #[CurrentUser] User $user,
        #[MapQueryParameter] int $page = 1,
        #[MapQueryParameter(name: 'section', filter: FILTER_DEFAULT, flags: FILTER_FLAG_EMPTY_STRING_NULL | FILTER_NULL_ON_FAILURE)] string|null $sectionUuid = null,
    ): Response {
        $sectionFilterView = $sectionFilter->handle($sectionUuid);
        $remarks = null;

        if($sectionFilterView->getCurrentSection() !== null) {
            $remarks = $this->repository->findBySectionPaginated(new PaginationQuery(page: $page, limit: self::ITEMS_PER_PAGE), $sectionFilterView->getCurrentSection(), $user);
        }

        return $this->render('books/report_remark/index.html.twig', [
            'remarks' => $remarks,
            'sectionFilter' => $sectionFilterView
        ]);
    }

    #[Route('/add', name: 'add_report_remark')]
    public function add(
        Request $request,
        SectionResolverInterface $sectionResolver,
        ReportRemarksSettings $settings,
    ): Response {
        $remark = new ReportRemark();
        $remark->setSection($sectionResolver->getCurrentSection());

        $form = $this->createForm(ReportRemarkType::class, $remark);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($remark);
            $this->addFlash('success', 'book.book_remarks.add.success');

            return $this->redirectToRoute('report_remarks');
        }

        return $this->render('books/report_remark/add.html.twig', [
            'form' => $form->createView(),
            'settings' => $settings,
        ]);
    }

    #[Route('/{uuid}/edit', name: 'edit_report_remark')]
    public function edit(
        #[MapEntity(mapping: ['uuid' => 'uuid'])] ReportRemark $remark,
        Request $request,
        ReportRemarksSettings $settings,
    ) {
        $this->denyAccessUnlessGranted(ReportRemarkVoter::EDIT, $remark);

        $form = $this->createForm(ReportRemarkType::class, $remark);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($remark);
            $this->addFlash('success', 'book.book_remarks.edit.success');

            return $this->redirectToRoute('report_remarks');
        }

        return $this->render('books/report_remark/edit.html.twig', [
            'form' => $form->createView(),
            'settings' => $settings,
        ]);
    }

    #[Route('/{uuid}/remove', name: 'remove_report_remark')]
    public function remove(
        #[MapEntity(mapping: ['uuid' => 'uuid'])] ReportRemark $remark,
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted(ReportRemarkVoter::REMOVE, $remark);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'book.report_remarks.remove.confirm',
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($remark);
            $this->addFlash('success', 'book.book_remarks.remove.success');
            return $this->redirectToRoute('report_remarks');
        }

        return $this->render('books/report_remark/remove.html.twig', [
            'form' => $form->createView(),
            'remark' => $remark
        ]);
    }
}
