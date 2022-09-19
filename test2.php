<?php

namespace App\Controller;

use App\Entity\UpdateUserInfo;
use App\Message\GeneratePdfPasswordMessage;
use App\Repository\DocumentRepository;
use App\Repository\TypeDocumentRepository;
use App\Repository\UpdateUserInfoRepository;
use App\Repository\UserRepository;
use App\Service\CoproService;
use App\Service\FlashMessageService;
use App\Service\UpdateUserService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    public function __construct(
        private CoproService $coproService,
        private UserRepository $userRepository,
        private UserService $userService,
        private MessageBusInterface $bus,
        private UpdateUserInfoRepository $updateUserInfoRepository,
        private UpdateUserService $updateUserService,
        private DocumentRepository $documentRepository,
        private TypeDocumentRepository $typeDocumentRepository,
        private FlashMessageService $flashMessageService,
    ) {
    }

    #[Route(path: '/admin', name: 'admin')]
    public function index(): Response
    {
        $copros = $this->coproService->getCopros();

        return $this->render('admin/index.html.twig', [
            'copros' => $copros,
            'users' => $this->userRepository->findAll(),
            'documents' => $this->documentRepository->count([]),
            'typesDocuments' => $this->typeDocumentRepository->count([]),
            'maj' => $this->updateUserInfoRepository->count(['isUpdated' => false]),
        ]);
    }

    #[Route(path: '/admin/users', name: 'admin_users')]
    public function users(): Response
    {
        return $this->render('admin/users.html.twig', [
            'coproprietaires' => $this->coproService->getComptes(),
            'users' => $this->userRepository->findAll(),
        ]);
    }

    #[Route(path: '/admin/campagne-login-pass/start', name: 'admin_campagne_login_pass_start')]
    public function campagneLoginPassStart(): Response
    {
        return $this->render('admin/campagne/start.html.twig', [
            'copros' => $this->coproService->getCopros(),
        ]);
    }

    #[Route(path: '/admin/campagne-login-pass/users/{id}', name: 'admin_campagne_login_pass_users')]
    public function campagneLoginPassUsers(string $id): Response
    {
        return $this->render('admin/campagne/users.html.twig', [
            'copro' => $this->coproService->getCopro($id),
            'users' => $this->userRepository->findAll(),
            'coproId' => $id,
        ]);
    }

    #[Route(path: '/admin/campagne-login-pass/generate/{id}/export', name: 'admin_campagne_login_pass_generate')]
    public function campagneLoginPassGenerate(string $id, Request $request): Response
    {
        $this->bus->dispatch(new GeneratePdfPasswordMessage($id, $request->getHttpHost()));

        $this->flashMessageService->addSuccessToastr(
            'La génération des mots de passe a été lancée, vous recevrez un mail lorsque la génération sera terminée.',
            'Bravo !'
        );

        return $this->redirectToRoute('admin_campagne_login_pass_start');
    }

    #[Route(path: '/admin/copro', name: 'admin_copro')]
    public function copro(): Response
    {
        return $this->render('admin/copro.html.twig', [
            'copros' => $this->coproService->getCopros(),
        ]);
    }

    #[Route(path: '/admin/copro/{id}', name: 'admin_detail_copro')]
    public function coproDetail(
        TypeDocumentRepository $typeDocumentRepository,
        DocumentRepository $documentRepository,
        int $id
    ): Response {
        return $this->render('admin/detail.copro.html.twig', [
            'copro' => $this->coproService->getCopro($id),
            'type_document' => $typeDocumentRepository->findAll(),
            'documents' => $documentRepository->findByCoproId($id),
        ]);
    }

    #[Route(path: '/admin/coproprietaire', name: 'admin_coproprietaire')]
    public function coproprietaire(): Response
    {
        $coproprietaires = $this->coproService->getCoproprietaires();

        return $this->render('admin/coproprietaire.html.twig', [
            'coproprietaires' => $coproprietaires,
        ]);
    }

    #[Route(path: '/admin/update', name: 'admin_update')]
    public function update(): Response
    {
        $updates = $this->updateUserInfoRepository->findBy(['isUpdated' => false]);

        return $this->render('admin/update.html.twig', [
            'updates' => $updates,
        ]);
    }

    #[Route(path: '/admin/update-ulis/{id}', name: 'admin_update_ulis')]
    public function updateUlis(UpdateUserInfo $updateUserInfo): Response
    {
        $updateUserInfo->setIsUpdated(true);
        $this->updateUserService->persistUpdateUserInfo($updateUserInfo);

        return $this->redirectToRoute('admin_update');
    }
}