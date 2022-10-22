<?php

namespace App\View\Filter;

use App\Entity\TeacherTag;
use App\Repository\TeacherTagRepositoryInterface;
use App\Security\Voter\TeacherTagVoter;
use App\Sorting\Sorter;
use App\Sorting\TeacherTagStrategy;
use App\Utils\ArrayUtils;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TeacherTagFilter {
    public function __construct(private Sorter $sorter, private TeacherTagRepositoryInterface $tagRepository, private TranslatorInterface $translator, private AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    public function handle(?string $tagUuid = null): TeacherTagFilterView {
        $tags = $this->tagRepository->findAll();
        $tags = array_filter($tags, fn(TeacherTag $tag) => $this->authorizationChecker->isGranted(TeacherTagVoter::View, $tag));
        $tags = $this->addImplicitTags($tags);


        $this->sorter->sort($tags, TeacherTagStrategy::class);

        $tags = ArrayUtils::createArrayWithKeys(
            $tags,
            fn(TeacherTag $tag) => $tag->getUuid()->toString()
        );

        $tag = $tagUuid !== null ?
            $tags[$tagUuid] ?? null : null;

        return new TeacherTagFilterView($tags, $tag);
    }

    private function addImplicitTags(array $tags) {
        $tags[] = TeacherTag::getGradeTeacherTag()
            ->setName($this->translator->trans('lists.teachers.tags.grade_teachers'));

        $tags[] = TeacherTag::getSubstituteGradeTeacherTag()
            ->setName($this->translator->trans('lists.teachers.tags.substitute_grade_teachers'));

        return $tags;
    }
}