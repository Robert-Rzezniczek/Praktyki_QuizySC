<?php

namespace App\Controller;

use App\Service\UserAuthServiceInterface;
use App\Service\UserProfileServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserEditController extends AbstractController
{
    #[Route('/user/edit', name: 'user_edit')]
    public function edit(
        Request $request,
        UserProfileServiceInterface $userProfileService,
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('danger', 'Musisz być zalogowany.');

            return $this->redirectToRoute('app_login');
        }

        $form = $userProfileService->buildEditForm($request, $user);

        if ($form->isSubmitted() && $form->isValid()) {
            $userProfileService->processEditForm($user);
            $this->addFlash('success', 'Dane zostały zaktualizowane.');

            return $this->redirectToRoute('app_menu');
        }

        return $this->render('edit/edit.html.twig', [
            'editForm' => $form->createView(),
        ]);
    }

    #[Route('/konto/usun', name: 'user_delete_account', methods: ['POST'])]
    public function deleteAccount(
        Request $request,
        UserAuthServiceInterface $userAuthService,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
    ): Response {
        if (!$this->isCsrfTokenValid('delete-account', $request->request->get('_token'))) {
            $this->addFlash('danger', 'Nieprawidłowy token CSRF.');

            return $this->redirectToRoute('user_edit');
        }

        $user = $this->getUser();

        $userAuthService->processDeleteAccount($user);
        $tokenStorage->setToken(null);
        $requestStack->getSession()->invalidate();

        $this->addFlash('success', 'Twoje konto zostało usunięte.');

        return $this->redirectToRoute('app_register');
    }
}
