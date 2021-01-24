<?php

namespace App\View\Filter;

use App\Entity\TeacherTag;
use App\Repository\TeacherTagRepositoryInterface;
use App\Security\Voter\TeacherTagVoter;
use App\Sorting\Sorter;
use App\Sorting\TeacherTagStrategy;
use App\Utils\ArrayUtils;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class TeacherTagFilter {
    private $sorter;
    private $tagRepository;

    private $authorizationChecker;

    public function __construct(Sorter $sorter, TeacherTagRepositoryInterface $tagRepository, AuthorizationCheckerInterface $authorizationChecker) {
        $this->sorter = $sorter;
        $this->tagRepository = $tagRepository;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function handle(?string $tagUuid = null): TeacherTagFilterView {
        $tags = $this->tagRepository->findAll();
        $tags = array_filter($tags, function(TeacherTag $tag) {
            return $this->authorizationChecker->isGranted(TeacherTagVoter::View, $tag);
        });
        $this->sorter->sort($tags, TeacherTagStrategy::class);

        $tags = ArrayUtils::createArrayWithKeys(
            $tags,
            function(TeacherTag $tag) {
                return $tag->getUuid()->toString();
            }
        );

        $tag = $tagUuid !== null ?
            $tags[$tagUuid] ?? null : null;

        return new TeacherTagFilterView($tags, $tag);
    }
}