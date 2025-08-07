<?php

/**
 * Registration controller.
 */

namespace App\Controller;

use App\Entity\UserAuth;
use App\Form\RegistrationForm;
use App\Service\UserAuthServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class RegistrationController.
 */
class RegistrationController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param UserAuthServiceInterface $userAuthService UserAuthService
     */
    public function __construct(private readonly UserAuthServiceInterface $userAuthService)
    {
    }

    /**
     * Register action.
     *
     * @param Request  $request  Request
     * @param Security $security Security
     *
     * @return Response HTTP response
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, Security $security): Response
    {
        $user = new UserAuth();
        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userAuthService->registerUser($user, $form);

            if ($this->userAuthService->isInvalidRegionSelected()) {
                $this->addFlash('warning', 'Wybrany powiat nie należy do wybranego województwa.');

                // Możesz przekierować z powrotem lub po prostu wyrenderować ten sam widok (jak poniżej)
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form,
                ]);
            }

            $this->addFlash('success', 'Zarejestrowano pomyślnie.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
