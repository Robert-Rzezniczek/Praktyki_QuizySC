<?php

/**
 * Quiz controller.
 */

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\QuizResult;
use App\Form\Type\QuizType;
use App\Service\QuizResultServiceInterface;
use App\Service\QuizServiceInterface;
use App\Service\UserAnswerServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class QuizController.
 */
#[Route('/quiz')]
class QuizController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param QuizServiceInterface       $quizService       QuizService
     * @param TranslatorInterface        $translator        TranslatorInterface
     * @param QuizResultServiceInterface $quizResultService QuizResultService
     * @param UserAnswerServiceInterface $userAnswerService UserAnswerService
     */
    public function __construct(private readonly QuizServiceInterface $quizService, private readonly TranslatorInterface $translator, private readonly QuizResultServiceInterface $quizResultService, private readonly UserAnswerServiceInterface $userAnswerService)
    {
    }

    /**
     * Index action.
     *
     * @param int $page Page number
     *
     * @return Response HTTP response
     */
    #[Route(
        name: 'quiz_index',
        methods: 'GET'
    )]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->quizService->getPaginatedList($page);

        return $this->render('quiz/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * View action.
     *
     * @param Quiz $quiz Quiz entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'quiz_view',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function view(Quiz $quiz): Response
    {
        return $this->render(
            'quiz/view.html.twig',
            ['quiz' => $quiz]
        );
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/quiz/create', name: 'quiz_create')]
    public function create(Request $request): Response
    {
        $quiz = new Quiz();

        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->quizService->save($quiz);
                $this->addFlash('success', 'Quiz został utworzony.');

                return $this->redirectToRoute('quiz_index');
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('quiz/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Quiz    $quiz    Quiz entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/edit',
        name: 'quiz_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|POST'
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Quiz $quiz): Response
    {
        $form = $this->createForm(QuizType::class, $quiz, [
            'method' => 'POST',
            'action' => $this->generateUrl('quiz_edit', ['id' => $quiz->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->quizService->save($quiz);
                $this->addFlash(
                    'success',
                    $this->translator->trans('message.edited_successfully')
                );

                return $this->redirectToRoute('quiz_index');
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render(
            'quiz/edit.html.twig',
            [
                'form' => $form->createView(),
                'quiz' => $quiz,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Quiz    $quiz    Quiz entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/delete',
        name: 'quiz_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|POST'
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Quiz $quiz): Response
    {
        if (!$this->quizService->canBeDeleted($quiz)) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.quiz_cannot_be_deleted')
            );

            return $this->redirectToRoute('quiz_index');
        }

        $form = $this->createForm(
            FormType::class,
            $quiz,
            [
                'method' => 'POST',
                'action' => $this->generateUrl('quiz_delete', ['id' => $quiz->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->quizService->delete($quiz);
            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('quiz_index');
        }

        return $this->render(
            'quiz/delete.html.twig',
            [
                'form' => $form->createView(),
                'quiz' => $quiz,
            ]
        );
    }


    /**
     * Before solve, begin action.
     *
     * @param Quiz $quiz Quiz
     *
     * @return Response
     */
    #[Route(
        '/{id}/view-quiz',
        name: 'quiz_view_quiz',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    #[IsGranted('ROLE_USER')]
    public function viewQuiz(Quiz $quiz): Response
    {
        return $this->render(
            'quiz/view_quiz.html.twig',
            ['quiz' => $quiz]
        );
    }


    /**
     * Solve quiz action.
     *
     * @param Quiz $quiz Quiz
     *
     * @return Response
     */
    #[Route(
        '/{id}/solve',
        name: 'quiz_solve',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function solve(Quiz $quiz): Response
    {
        return $this->render(
            'quiz/solve_quiz.html.twig',
            ['quiz' => $quiz]
        );
    }

    /**
     * Create step action (step by step quiz).
     *
     * @param Request $request HTTP request
     * @param int     $step    Current step (1, 2, 3, etc.)
     *
     * @return Response HTTP response
     */
    #[Route('/create/step/{step}', name: 'quiz_create_step', requirements: ['step' => '\d+'], methods: 'GET|POST')]
    public function createStep(Request $request, int $step = 1): Response
    {
        $quiz = $this->quizService->initializeQuizFromSession($request->getSession());
        $form = $this->createForm(QuizType::class, $quiz, [
            'step' => $step,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->quizService->saveQuizToSession($quiz, $request->getSession());

            if (3 > $step) {
                return $this->redirectToRoute('quiz_create_step', ['step' => $step + 1]);
            }

            // Ostatni krok - zapis
            $quiz->setTimeLimit($quiz->getTimeLimit());
            $this->quizService->save($quiz);
            $request->getSession()->remove('quiz_data');
            $this->addFlash('success', 'Quiz został utworzony.');

            return $this->redirectToRoute('quiz_index');
        }

        return $this->render('quiz/create.html.twig', [
            'form' => $form->createView(),
            'step' => $step,
            'maxSteps' => 3,
        ]);
    }

    /**
     * Edit step action (step by step quiz editing).
     *
     * @param Request $request HTTP request
     * @param Quiz    $quiz    Quiz entity
     * @param int     $step    Current step (1, 2, 3)
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/edit/step/{step}',
        name: 'quiz_edit_step',
        requirements: ['id' => '[1-9]\d*', 'step' => '\d+'],
        methods: ['GET', 'POST']
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function editStep(Request $request, Quiz $quiz, int $step = 1): Response
    {
        $maxSteps = 3;

        // Walidacja kroku
        if ($step < 1 || $step > $maxSteps) {
            throw $this->createNotFoundException('Nieprawidłowy krok.');
        }

        // Ładowanie quizu z uwzględnieniem danych z sesji
        $quiz = $this->quizService->initializeQuizFromSession($request->getSession(), $quiz);

        // Tworzenie formularza dla danego kroku
        $form = $this->createForm(QuizType::class, $quiz, [
            'step' => $step,
            'action' => $this->generateUrl('quiz_edit_step', ['id' => $quiz->getId(), 'step' => $step]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Zapis do sesji między krokami
                $this->quizService->saveQuizToSession($quiz, $request->getSession());

                if ($step < $maxSteps) {
                    // Przejdź do następnego kroku
                    return $this->redirectToRoute('quiz_edit_step', [
                        'id' => $quiz->getId(),
                        'step' => $step + 1,
                    ]);
                }

                // Ostatni krok - zapis do bazy danych
                $this->quizService->save($quiz);
                $request->getSession()->remove('quiz_data');
                $this->addFlash('success', $this->translator->trans('message.edited_successfully'));

                return $this->redirectToRoute('quiz_index');
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('quiz/edit.html.twig', [
            'form' => $form->createView(),
            'step' => $step,
            'maxSteps' => $maxSteps,
            'quiz' => $quiz,
        ]);
    }

    /**
     * Publish quiz action.
     *
     * @param Request $request HTTP request
     * @param Quiz    $quiz    Quiz entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/publish',
        name: 'quiz_publish',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'POST']
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function publish(Request $request, Quiz $quiz): Response
    {
        $form = $this->createForm(FormType::class, null, [
            'method' => 'POST',
            'action' => $this->generateUrl('quiz_publish', ['id' => $quiz->getId()]),
        ]);
        $form->add('duration', IntegerType::class, [
            'label' => 'Czas publikacji (w minutach)',
            'required' => true,
            'constraints' => [
                new GreaterThan(['value' => 0, 'message' => 'Czas publikacji musi być większy od zera.']),
            ],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $duration = $form->get('duration')->getData();
                $this->quizService->publishQuiz($quiz, $duration, $request->getSession());
                $this->addFlash('success', $this->translator->trans('message.quiz_published_successfully'));

                return $this->redirectToRoute('quiz_index');
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('quiz/publish.html.twig', [
            'form' => $form->createView(),
            'quiz' => $quiz,
        ]);
    }

    /**
     * Check quiz status action.
     *
     * @param Request $request HTTP request
     * @param Quiz    $quiz    Quiz entity
     *
     * @return JsonResponse JSON response
     */
    #[Route(
        '/{id}/status',
        name: 'quiz_check_status',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET']
    )]
    public function checkStatus(Request $request, Quiz $quiz): JsonResponse
    {
        $status = $this->quizService->checkQuizStatus($quiz, $request->getSession());

        return new JsonResponse($status);
    }


    /**
     * Save Result action.
     *
     * @param Quiz                       $quiz
     * @param QuizResultServiceInterface $quizResultService
     *
     * @return Response
     */
    #[Route('/{id}/save-result', name: 'quiz_save_result', methods: ['POST'])]
    public function saveResult(Quiz $quiz, QuizResultServiceInterface $quizResultService): Response
    {
        $user = $this->getUser();

        $result = new QuizResult();
        $result->setUser($user);
        $result->setQuiz($quiz);
        $result->setScore(90);
        $result->setCorrectAnswers(18);
        $result->setTotalTime(120);
        // $result->setStartedAt(new \DateTime('-5 minutes'));
        // $result->setCompletedAt(new \DateTime());
        // $result->setExpiresAt(new \DateTime('+1 hour')); bo chyba ty to zrobiles

        $quizResultService->save($result);

        $this->addFlash('success', 'Wynik quizu zapisany.');

        return $this->redirectToRoute('quiz_view', ['id' => $quiz->getId()]);
    }

    /**
     * Edycja brandingu (tylko dla admina).
     */
    #[Route('/quiz/{id}/branding/edit', name: 'quiz_edit_branding', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editBranding(Request $request, Quiz $quiz): Response
    {
        $form = $this->createForm(QuizType::class, $quiz, [
            'branding_only' => true,
            'method' => 'POST',
            'action' => $this->generateUrl('quiz_edit_branding', ['id' => $quiz->getId()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['logoFile']->getData();

            if ($uploadedFile) {
                $newFilename = uniqid().'.'.$uploadedFile->guessExtension();
                $uploadDir = $this->getParameter('uploads_directory');

                $uploadedFile->move($uploadDir, $newFilename);
                $quiz->setLogoFilename($newFilename);
            }

            $this->quizService->saveBranding($quiz);

            $this->addFlash('success', 'Branding zapisany.');

            return $this->redirectToRoute('quiz_view', ['id' => $quiz->getId()]);
        }

        return $this->render('quiz/edit.html.twig', [
            'form' => $form->createView(),
            'quiz' => $quiz,
            'brandingEdit' => true,
        ]);
    }

    /**
     * start quizu
     * @param Quiz $quiz quiz
     *
     * @return Response
     */
    #[Route('/{id}/start-view', name: 'quiz_start_view', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function startView(Quiz $quiz): Response
    {
        return $this->render('quiz/start_view.html.twig', [
            'quiz' => $quiz,
        ]);
    }
}
