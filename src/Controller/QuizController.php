<?php

/**
 * Quiz controller.
 */

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\UserAuth;
use App\Form\Type\QuizSolveType;
use App\Form\Type\QuizType;
use App\Service\QuizResultServiceInterface;
use App\Service\QuizServiceInterface;
use App\Service\UserAnswerServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
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
     * @param QuizServiceInterface       $quizService       QuizServiceInterface
     * @param TranslatorInterface        $translator        TranslatorInterface
     * @param QuizResultServiceInterface $quizResultService QuizResultServiceInterface
     * @param UserAnswerServiceInterface $userAnswerService UserAnswerServiceInterface
     * @param RequestStack               $requestStack      RequestStack
     */
    public function __construct(private readonly QuizServiceInterface $quizService, private readonly TranslatorInterface $translator, private readonly QuizResultServiceInterface $quizResultService, private readonly UserAnswerServiceInterface $userAnswerService, private readonly RequestStack $requestStack)
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
        $this->quizService->updateExpiredQuizzes();
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
     * @return Response Response
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
     * @param Request            $request Request
     * @param Quiz               $quiz    Quiz
     * @param SessionInterface   $session SessionInterface
     * @param UserInterface|null $user    UserInterface|null
     *
     * @return Response Response
     */
    #[Route('/{id}/solve', name: 'quiz_solve', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function solve(Request $request, Quiz $quiz, SessionInterface $session, ?UserInterface $user): Response
    {
        if (!$user instanceof UserAuth) {
            throw $this->createAccessDeniedException('Użytkownik nie jest zalogowany.');
        }

        // Sprawdź, czy quiz jest opublikowany
        if (!$this->quizService->isQuizPublished($quiz, $session)) {
            try {
                $startedAtTimestamp = $session->get(sprintf('quiz_%d_start_time', $quiz->getId()));
                $startedAt = $startedAtTimestamp ? (new \DateTime())->setTimestamp($startedAtTimestamp) : new \DateTime();
                $quizResult = $this->quizService->finalizeQuiz($quiz, $user, $session, $quiz->getTimeLimit() ?? 30, $startedAt);
                $this->addFlash('warning', 'message.quiz_expired');

                return $this->redirectToRoute('quiz_index');
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        if (!$this->quizService->canUserSolveQuiz($quiz, $user)) {
            $this->addFlash('error', 'message.quiz_already_solved');

            return $this->redirectToRoute('quiz_index');
        }

        // Inicjalizuj sesję quizu i zapisz czas rozpoczęcia
        $questionIds = $this->quizService->initializeQuizSession($quiz, $session);
        $currentIndex = $request->query->getInt('question_index', $request->request->getInt('question_index', 0));

        // Zapisz czas rozpoczęcia, jeśli to pierwsze pytanie
        if (0 === $currentIndex && !$session->has(sprintf('quiz_%d_start_time', $quiz->getId()))) {
            $session->set(sprintf('quiz_%d_start_time', $quiz->getId()), (new \DateTime())->getTimestamp());
        }

        // Pobierz kolejne pytanie
        $question = $this->quizService->getNextQuestion($quiz, $currentIndex, $session);

        // Sprawdź, czy czas się skończył
        if ($question && $this->quizService->isTimeLimitExceeded($quiz, $session)) {
            try {
                $startedAtTimestamp = $session->get(sprintf('quiz_%d_start_time', $quiz->getId()));
                $startedAt = $startedAtTimestamp ? (new \DateTime())->setTimestamp($startedAtTimestamp) : new \DateTime();
                $quizResult = $this->quizService->finalizeQuiz($quiz, $user, $session, $quiz->getTimeLimit() ?? 30, $startedAt);
                $this->addFlash('warning', 'message.time_limit_exceeded_saved');
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(['success' => true]);
                }

                return $this->redirectToRoute('quiz_index');
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('error', $e->getMessage());
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(['error' => $e->getMessage()], 400);
                }
            }
        }

        // Sprawdź, czy wszystkie pytania zostały odpowiedziane
        if (!$question) {
            // Zapisz wynik quizu
            try {
                $startedAtTimestamp = $session->get(sprintf('quiz_%d_start_time', $quiz->getId()));
                $startedAt = $startedAtTimestamp ? (new \DateTime())->setTimestamp($startedAtTimestamp) : new \DateTime();
                $quizResult = $this->quizService->finalizeQuiz($quiz, $user, $session, $quiz->getTimeLimit() ?? 30, $startedAt);
                $this->addFlash('success', 'message.quiz_completed');
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(['success' => true]);
                }

                return $this->redirectToRoute('quiz_result', ['id' => $quizResult->getId()]);
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('error', $e->getMessage());
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(['error' => $e->getMessage()], 400);
                }
            }

            return $this->redirectToRoute('quiz_index');
        }

        // Obsługa przesłanego formularza (w tym żądanie AJAX z JS)
        $form = $this->createForm(QuizSolveType::class, null, [
            'question' => $question,
            'question_index' => $currentIndex,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            try {
                $this->quizService->saveUserAnswer($quiz, $user, $question->getId(), $data['answer'], $session);
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(['success' => true]);
                }

                return $this->redirectToRoute('quiz_solve', [
                    'id' => $quiz->getId(),
                    'question_index' => $currentIndex + 1,
                ]);
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('error', $e->getMessage());
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(['error' => $e->getMessage()], 400);
                }
            }
        }

        // Przekazanie czasu rozpoczęcia i limitu do szablonu
        $startedAtTimestamp = $session->get(sprintf('quiz_%d_start_time', $quiz->getId()));
        $timeLimit = $quiz->getTimeLimit() ?? 30; // Domyślny limit 30 minut, jeśli nie ustawiono

        return $this->render('quiz/solve_quiz.html.twig', [
            'quiz' => $quiz,
            'question' => $question,
            'form' => $form->createView(),
            'is_last_question' => $currentIndex === count($questionIds) - 1,
            'started_at' => $startedAtTimestamp,
            'time_limit' => $timeLimit,
        ]);
    }

    /**
     * Show quiz result action.
     *
     * @param int $id QuizResult ID
     *
     * @return Response HTTP response
     */
    #[Route('/result/{id}', name: 'quiz_result', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    #[IsGranted('ROLE_USER')]
    public function showResult(int $id): Response
    {
        $user = $this->getUser();
        $quizResult = $this->quizResultService->getQuizResultForUser($id, $user);
        if (!$quizResult) {
            throw $this->createAccessDeniedException('Nie masz dostępu do tych wyników.');
        }

        $quiz = $quizResult->getQuiz();
        $score = $quizResult->getScore();
        $correctAnswers = $quizResult->getCorrectAnswers();
        $totalQuestions = $quiz->getQuestions()->count();
        $startedAt = $quizResult->getStartedAt() ? $quizResult->getStartedAt()->getTimestamp() : (new \DateTime())->getTimestamp();
        $completedAt = $quizResult->getCompletedAt() ? $quizResult->getCompletedAt()->getTimestamp() : (new \DateTime())->getTimestamp();
        $duration = $completedAt - $startedAt;

        // NOWE: Obliczenie punktów
        $totalPoints = 0;
        foreach ($quiz->getQuestions() as $question) {
            $totalPoints += $question->getPoints();
        }
        $earnedPoints = round(($score / 100) * $totalPoints);


        return $this->render('quiz/result.html.twig', [
            'quiz' => $quiz,
            'quizResult' => $quizResult,
            'score' => $score,
            'correctAnswers' => $correctAnswers,
            'totalQuestions' => $totalQuestions,
            'duration' => $duration,
            'earnedPoints' => $earnedPoints,
            'totalPoints' => $totalPoints,
        ]);
    }

    /**
     * Create step action (step by step quiz).
     *
     * @param Request $request HTTP request
     * @param int     $step    Current step (1, 2, 3, etc.)
     *
     * @return Response HTTP response
     */
    #[Route('/create/step/{step}', name: 'quiz_create_step', requirements: ['step' => '\d+'], methods: ['GET', 'POST'])]
    public function createStep(Request $request, int $step = 1): Response
    {
        $session = $request->getSession();

        // Quiz z sesji – TYLKO DO WYŚWIETLENIA FORMULARZA
        $quiz = $this->quizService->initializeQuizFromSession($session);

        // Formularz z tym quizem
        $form = $this->createForm(QuizType::class, $quiz, [
            'step' => $step,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Zapisz TYLKO wtedy, gdy formularz został przesłany i jest poprawny
            $this->quizService->saveQuizToSession($quiz, $session);

            if ($step < 3) {
                return $this->redirectToRoute('quiz_create_step', ['step' => $step + 1]);
            }

            // Ostatni krok – zapisz do bazy
            $this->quizService->save($quiz);
            $session->remove('quiz_data');
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
        '/{id}/check-status',
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
     * Cancel create quiz action. (clears the session).
     *
     * @param SessionInterface $session SessionInterface
     *
     * @return Response Response
     */
    #[Route('/create/cancel', name: 'quiz_create_cancel')]
    public function cancelCreate(SessionInterface $session): Response
    {
        $session->remove('quiz_data');

        return $this->redirectToRoute('quiz_index');
    }

    /**
     * Edycja brandingu (tylko dla admina).
     *
     * @param Request $request Request
     * @param Quiz    $quiz    Quiz
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
     * start quizu.
     *
     * @param Quiz $quiz Quiz
     *
     * @return Response Response
     */
    #[Route('/{id}/start-view', name: 'quiz_start_view', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function startView(Quiz $quiz): Response
    {
        return $this->render('quiz/start_view.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    /**
     * Show quiz ranking action.
     *
     * @param Quiz $quiz Quiz entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/ranking',
        name: 'quiz_ranking',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    #[IsGranted('ROLE_USER')]
    public function showRanking(Quiz $quiz): Response
    {
        $user = $this->getUser();

        if (!$user instanceof UserAuth) {
            throw $this->createAccessDeniedException('Musisz być zalogowany, aby zobaczyć ranking.');
        }

        $rankingData = $this->quizService->getQuizRanking($quiz, $user);
        $quizResult = $this->quizService->getQuizResultForQuizAndUser($quiz, $user);

        return $this->render('quiz/ranking.html.twig', [
            'quiz' => $quiz,
            'result_id' => $quizResult ? $quizResult->getId() : null,
            'ranking' => $rankingData['ranking'],
            'userPosition' => $rankingData['userPosition'],
            'userScore' => $rankingData['userScore'],
        ]);
    }

    #[Route('/menu', name: 'quiz_menu_view', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function menuView(): Response
    {
        $user = $this->getUser();
        $quizzes = $this->quizService->prepareMenuViewData($user);

        return $this->render('quiz/menuView.html.twig', [
            'quizzes' => $quizzes,
        ]);
    }
}
