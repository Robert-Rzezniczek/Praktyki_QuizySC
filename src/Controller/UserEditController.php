<?php

/**
 * UserEdit controller.
 */

namespace App\Controller;

use App\Service\UserAuthServiceInterface;
use App\Service\UserProfileServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * UserEditController class.
 */
class UserEditController extends AbstractController
{
    /**
     * Construct.
     *
     * @param UserProfileServiceInterface $userProfileService UserProfileServiceInterface
     * @param UserAuthServiceInterface    $userAuthService    UserAuthServiceInterface
     * @param TokenStorageInterface       $tokenStorage       TokenStorageInterface
     * @param RequestStack                $requestStack       RequestStack
     */
    public function __construct(private readonly UserProfileServiceInterface $userProfileService, private readonly UserAuthServiceInterface $userAuthService, private readonly TokenStorageInterface $tokenStorage, private readonly RequestStack $requestStack)
    {
    }

    /**
     * Edit action.
     *
     * @param Request $request Request
     *
     * @return Response Response
     */
    #[Route('/user/edit', name: 'user_edit')]
    public function edit(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('danger', 'Musisz być zalogowany.');

            return $this->redirectToRoute('app_login');
        }

        $form = $this->userProfileService->buildEditForm($request, $user);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userProfileService->processEditForm($user);
            $this->addFlash('success', 'Dane zostały zaktualizowane.');

            return $this->redirectToRoute('app_menu');
        }

        return $this->render('edit/edit.html.twig', [
            'editForm' => $form->createView(),
        ]);
    }

    /**
     * Delete account action.
     *
     * @param Request $request Request
     *
     * @return Response Response
     */
    #[Route('/konto/usun', name: 'user_delete_account', methods: ['POST'])]
    public function deleteAccount(Request $request): Response
    {
        if (!$this->isCsrfTokenValid('delete-account', $request->request->get('_token'))) {
            $this->addFlash('danger', 'Nieprawidłowy token CSRF.');

            return $this->redirectToRoute('user_edit');
        }

        $user = $this->getUser();

        $this->userAuthService->processDeleteAccount($user);
        $this->tokenStorage->setToken(null);
        $this->requestStack->getSession()->invalidate();

        $this->addFlash('success', 'Twoje konto zostało usunięte.');

        return $this->redirectToRoute('app_register');
    }
}
