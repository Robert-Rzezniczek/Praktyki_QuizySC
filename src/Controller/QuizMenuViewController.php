<?php

namespace App\Controller;

use App\Repository\QuizRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuizMenuViewController extends AbstractController
{
    public function __construct(private readonly QuizRepository $quizRepository)
    {
    }

    #[Route('/quizzes', name: 'app_quiz_list')]
    public function list(): Response
    {
        // Jeśli admin → przekieruj do panelu admina (index z opcją dodawania/edycji quizów)
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('quiz_index');
        }

        // Użytkownik widzi tylko quizy opublikowane
        $quizzes = $this->quizRepository->findBy(['isPublished' => true]);

        return $this->render('quiz/menuView.html.twig', [
            'quizzes' => $quizzes,
        ]);
    }

    #[Route('/quiz/{id}/start-view', name: 'app_quiz_start')]
    public function start(int $id): Response
    {
        // Tutaj możesz załadować pełny obiekt quizu z bazy, jeśli chcesz pokazać branding
        $quiz = $this->quizRepository->find($id);

        if (!$quiz || !$quiz->isPublished()) {
            throw $this->createNotFoundException('Quiz nie istnieje lub nie został opublikowany.');
        }

        return $this->render('quiz/start_view.html.twig', [
            'quiz' => $quiz,
        ]);
    }
}
