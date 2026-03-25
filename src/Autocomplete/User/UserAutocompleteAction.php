<?php

declare(strict_types=1);

namespace App\Autocomplete\User;

use App\Autocomplete\Response;
use App\Repository\PaginationQuery;
use App\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class UserAutocompleteAction extends AbstractController {

    #[Route(path: '/autocomplete/user', name: 'autocomplete_user')]
    #[IsGranted('ROLE_ALLOWED_TO_SWITCH')]
    public function __invoke(
        UserRepositoryInterface $userRepository,
        UserTransformator $transformator,
        #[MapQueryParameter(name: 'q')] string $query,
        #[MapQueryParameter] int|null $page = 1,
        #[MapQueryParameter] int|null $limit = 25,
    ): JsonResponse {
        $users = $userRepository->findAllPaginated(new PaginationQuery(page: $page, limit: $limit), $query);

        $items = [ ];

        foreach($users as $user) {
            $items[] = $transformator->transform($user);
        }

        return $this->json(
            new Response(
                $page,
                $users->getTotalPages(),
                $users->totalCount,
                $items
            )
        );
    }
}
