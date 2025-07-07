<?php

namespace App\Controller;

use App\Entity\UserAuth;
use App\Form\UserEditForm;
use App\Form\UserProfileType;
use App\Form\AdminPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/user')]
#[IsGranted('ROLE_ADMIN')]
class AdminUserController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    #[Route('/', name: 'admin_user_list')]
    public function list(): Response
    {
        $users = $this->em->getRepository(UserAuth::class)->findAll();

        return $this->render('admin_user/list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_user_edit')]
    public function edit(UserAuth $user, Request $request): Response
    {
        $profile = $user->getProfile();

        $authForm = $this->createForm(UserEditForm::class, $profile);
        $profileForm = $this->createForm(UserProfileType::class, $profile);
        $passwordForm = $this->createForm(AdminPasswordType::class);

        $authForm->handleRequest($request);
        $profileForm->handleRequest($request);
        $passwordForm->handleRequest($request);

        if ($authForm->isSubmitted() && $authForm->isValid() && $profileForm->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Dane użytkownika zostały zaktualizowane.');
            return $this->redirectToRoute('admin_user_list');
        }

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $newPassword = $passwordForm->get('plainPassword')->getData();
            if ($newPassword) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
                $this->em->flush();
                $this->addFlash('success', 'Hasło zostało zmienione.');
                return $this->redirectToRoute('admin_user_list');
            } else {
                $passwordForm->get('plainPassword')->addError(new FormError('Hasło nie może być puste.'));
            }
        }

        return $this->render('admin_user/edit.html.twig', [
            'user' => $user,
            'authForm' => $authForm->createView(),
            'profileForm' => $profileForm->createView(),
            'passwordForm' => $passwordForm->createView(),
        ]);
    }
}
