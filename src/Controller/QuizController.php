<?php

/**
 * Quiz controller.
 */

namespace App\Controller;

use App\Entity\Quiz;
use App\Form\Type\QuizType;
use App\Service\QuizServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
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
    public function __construct(private readonly QuizServiceInterface $quizService, private readonly TranslatorInterface $translator)
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
    #[Route(
        '/create',
        name: 'quiz_create',
        methods: 'GET|POST'
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): Response
    {
        $quiz = new Quiz();
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dump($quiz); // Debug całego obiektu quiz
            try {
                $this->quizService->save($quiz);
                $this->addFlash(
                    'success',
                    $this->translator->trans('message.created_successfully')
                );

                return $this->redirectToRoute('quiz_index');
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render(
            'quiz/create.html.twig',
            ['form' => $form->createView()]
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
    public function edit(Request $request, Quiz $quiz, QuizServiceInterface $quizService): Response
    {
        $form = $this->createForm(QuizType::class, $quiz, [
            'method' => 'POST',
            'action' => $this->generateUrl('quiz_edit', ['id' => $quiz->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $quizService->save($quiz);
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
}
