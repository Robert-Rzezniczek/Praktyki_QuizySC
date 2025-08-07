<?php

/**
 *  AdminUser controller.
 */

namespace App\Controller;

use App\Repository\UserAnswerRepository;
use App\Service\UserAuthService;
use App\Service\UserProfileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Form\FormError;
use App\Form\UserEditForm;
use App\Form\UserProfileType;
use App\Form\AdminPasswordType;
use App\Entity\UserAuth;

/**
 * AdminUserController class.
 */
#[Route('/admin/user')]
#[IsGranted('ROLE_ADMIN')]
class AdminUserController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param UserAuthService      $userService          UserAuthService
     * @param UserProfileService   $profileService       UserProfileService
     * @param UserAnswerRepository $userAnswerRepository UserAnswerRepository
     */
    public function __construct(private readonly UserAuthService $userService, private readonly UserProfileService $profileService, private readonly UserAnswerRepository $userAnswerRepository)
    {
    }

    /**
     * List action.
     *
     * @return Response Response
     */
    #[Route('/', name: 'admin_user_list')]
    public function list(): Response
    {
        $users = $this->userService->getAll();

        return $this->render('admin_user/list.html.twig', ['users' => $users]);
    }

    /**
     * Edit action.
     *
     * @param UserAuth $user    UserAuth
     * @param Request  $request Request
     *
     * @return Response Response
     */
    #[Route('/{id}/edit', name: 'admin_user_edit')]
    public function edit(UserAuth $user, Request $request): Response
    {
        $profile = $user->getProfile();
        if (!$profile) {
            $profile = $this->profileService->createEmptyProfile($user);
        }

        $authForm = $this->createForm(UserEditForm::class, $user);
        $profileForm = $this->createForm(UserProfileType::class, $profile);
        $passwordForm = $this->createForm(AdminPasswordType::class);

        $authForm->handleRequest($request);
        $profileForm->handleRequest($request);
        $passwordForm->handleRequest($request);

        if ($authForm->isSubmitted() && $authForm->isValid()) {
            $this->userService->save($user);
            $this->addFlash('success', 'Dane logowania zostały zapisane.');

            return $this->redirectToRoute('admin_user_edit', ['id' => $user->getId()]);
        }

        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $this->profileService->save($profile);
            $this->addFlash('success', 'Dane profilu zostały zapisane.');

            return $this->redirectToRoute('admin_user_edit', ['id' => $user->getId()]);
        }

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $newPassword = $passwordForm->get('plainPassword')->getData();
            if ($newPassword) {
                $this->userService->changePassword($user, $newPassword);
                $this->addFlash('success', 'Hasło zostało zmienione.');

                return $this->redirectToRoute('admin_user_edit', ['id' => $user->getId()]);
            }
            $passwordForm->get('plainPassword')->addError(new FormError('Hasło nie może być puste.'));
        }

        return $this->render('admin_user/edit.html.twig', [
            'user' => $user,
            'authForm' => $authForm->createView(),
            'profileForm' => $profileForm->createView(),
            'passwordForm' => $passwordForm->createView(),
        ]);
    }

    /**
     * Delete action.
     *
     * @param UserAuth $user    UserAuth
     * @param Request  $request Request
     *
     * @return Response Response
     */
    #[Route('/{id}/delete', name: 'admin_user_delete', methods: ['POST'])]
    public function delete(UserAuth $user, Request $request): Response
    {
        $userAnswers = $this->userAnswerRepository->findBy(['user' => $user]);

        if (!$this->isCsrfTokenValid('delete-user-'.$user->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Nieprawidłowy token CSRF.');

            return $this->redirectToRoute('admin_user_list');
        }
        // Blokada usuwania jeśli są odpowiedzi
        if (!empty($userAnswers)) {
            $this->addFlash('warning', 'Nie można usunąć użytkownika, który brał udział w testach.');

            return $this->redirectToRoute('admin_user_list');
        }

        $this->userService->delete($user);
        $this->addFlash('success', 'Użytkownik został usunięty.');

        return $this->redirectToRoute('admin_user_list');
    }
}
