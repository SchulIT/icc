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
    private $sorter;
    private $tagRepository;

    private $translator;
    private $authorizationChecker;

    public function __construct(Sorter $sorter, TeacherTagRepositoryInterface $tagRepository, TranslatorInterface $translator, AuthorizationCheckerInterface $authorizationChecker) {
        $this->sorter = $sorter;
        $this->tagRepository = $tagRepository;
        $this->translator = $translator;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function handle(?string $tagUuid = null): TeacherTagFilterView {
        $tags = $this->tagRepository->findAll();
        $tags = array_filter($tags, function(TeacherTag $tag) {
            return $this->authorizationChecker->isGranted(TeacherTagVoter::View, $tag);
        });
        $tags = $this->addImplicitTags($tags);


        $this->sorter->sort($tags, TeacherTagStrategy::class);

        $tags = ArrayUtils::createArrayWithKeys(
            $tags,
            function(TeacherTag $tag) {
                return $tag->getUuid()->toString();
            }
        );
        dump($tags);

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