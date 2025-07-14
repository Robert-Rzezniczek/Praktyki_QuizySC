<?php

/**
 * Quiz controller.
 */

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\QuizResult;
use App\Entity\UserAnswer;
use App\Entity\UserProfile;
use App\Entity\Question;
use App\Entity\Answer;
use App\Form\Type\QuizType;
use App\Service\QuizServiceInterface;
use App\Service\QuizResultServiceInterface;
use App\Service\UserAnswerServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
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
     * @param QuizServiceInterface $quizService Quiz service
     * @param TranslatorInterface  $translator  Translator
     */
    public function __construct(
        private readonly QuizServiceInterface $quizService,
        private readonly TranslatorInterface $translator,
        private readonly QuizResultServiceInterface $quizResultService,
        private readonly UserAnswerServiceInterface $userAnswerService
    ) {
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
    #[Route('/{id}/save-result', name: 'quiz_save_result', methods: ['POST'])]
    public function saveResult(
        Quiz $quiz,
        QuizResultServiceInterface $quizResultService,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();

        $result = new QuizResult();
        $result->setUser($user);
        $result->setQuiz($quiz);
        $result->setScore(90);
        $result->setCorrectAnswers(18);
        $result->setTotalTime(120);
        //$result->setStartedAt(new \DateTime('-5 minutes'));
        //$result->setCompletedAt(new \DateTime());
        //$result->setExpiresAt(new \DateTime('+1 hour')); bo chyba ty to zrobiles

        $quizResultService->save($result);

        $this->addFlash('success', 'Wynik quizu zapisany.');

        return $this->redirectToRoute('quiz_view', ['id' => $quiz->getId()]);
    }
}
