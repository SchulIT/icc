<?php

namespace App\Controller;

use App\Entity\OAuthClientInfo;
use App\Form\OAuthClientInfoType;
use App\Repository\OAuthClientInfoRepositoryInterface;
use App\Utils\SecurityUtilsInterface;
use Ramsey\Uuid\Uuid;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Trikoder\Bundle\OAuth2Bundle\Manager\ClientManagerInterface;
use Trikoder\Bundle\OAuth2Bundle\Model\Client;
use Trikoder\Bundle\OAuth2Bundle\Model\Grant;
use Trikoder\Bundle\OAuth2Bundle\Model\RedirectUri;
use Trikoder\Bundle\OAuth2Bundle\Model\Scope;

/**
 * @Route("/admin/apps")
 * @IsGranted("ROLE_ADMIN")
 */
class AppsAdminController extends AbstractController {

    private $clientManager;
    private $repository;

    public function __construct(ClientManagerInterface $clientManager, OAuthClientInfoRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);
        $this->clientManager = $clientManager;
        $this->repository = $repository;
    }

    /**
     * @Route("", name="admin_apps")
     */
    public function index() {
        $info = $this->repository->findAll();

        return $this->render('admin/apps/index.html.twig', [
            'apps' => $info
        ]);
    }

    /**
     * @Route("/add", name="add_app")
     */
    public function add(Request $request, SecurityUtilsInterface $securityUtils) {
        $info = new OAuthClientInfo();
        $client = new Client(Uuid::uuid4(), $securityUtils->generateRandom(256));
        $info->setClient($client);
        $form = $this->createForm(OAuthClientInfoType::class, $info, ['client' => false]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $client->setGrants(new Grant('authorization_code'));
            $this->clientManager->save($client);
            $this->repository->persist($info);

            $this->addFlash('success', 'admin.apps.add.success');
            return $this->redirectToRoute('edit_app', [
                'uuid' => $info->getUuid()
            ]);
        }

        return $this->render('admin/apps/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{uuid}/edit", name="edit_app")
     */
    public function edit(OAuthClientInfo $info, Request $request, SecurityUtilsInterface $securityUtils) {
        $form = $this->createForm(OAuthClientInfoType::class, $info);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $client = $info->getClient();

            if($client === null) {
                $client = new Client(Uuid::uuid4(), $securityUtils->generateRandom(256));
                $info->setClient($client);
            }

            $this->updateClientFromForm($client, $form);

            $this->clientManager->save($client);
            $this->repository->persist($info);

            $this->addFlash('success', 'admin.apps.edit.success');
            return $this->redirectToRoute('admin_apps');
        }

        return $this->render('admin/apps/edit.html.twig', [
            'info' => $info,
            'form' => $form->createView()
        ]);
    }

    private function updateClientFromForm(Client $client, FormInterface $form) {
        $client->setActive($form->get('active')->getData());

        $grants = array_map(
            function(string $grant) {
                return new Grant($grant);
            },
            $form->get('grants')->getData()
        );

        $scopes = array_map(
            function(string $scope) {
                return new Scope($scope);
            },
            $form->get('scopes')->getData()
        );

        $redirectUris = array_map(
            function(string $uri) {
                return new RedirectUri($uri);
            },
            array_filter(
                $form->get('redirect_uris')->getData(),
                function($input) {
                    return $input !== null;
                }
            )
        );

        $client->setScopes(...$scopes);
        $client->setRedirectUris(...$redirectUris);
        $client->setGrants(...$grants);
    }

    /**
     * @Route("/{uuid}/remove", name="remove_app")
     */
    public function remove(OAuthClientInfo $info, Request $request) {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'admin.apps.remove.confirm',
            'message_parameters' => [
                '%name%' => $info->getName()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->clientManager->remove($info->getClient()); // automatically removes OAuthClientInfo entity

            $this->addFlash('success', 'admin.apps.remove.success');

            return $this->redirectToRoute('admin_apps');
        }

        return $this->render('admin/apps/remove.html.twig', [
            'info' => $info,
            'form' => $form->createView()
        ]);
    }
}