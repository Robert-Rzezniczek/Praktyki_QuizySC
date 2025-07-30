<?php

/**
 *  Quiz Menu View Controller.
 */

namespace App\Controller;

use App\Repository\QuizRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller odpowiedzialny za widok listy quizów dla użytkownika.
 */
class QuizMenuViewController extends AbstractController
{
    /**
     * Konstruktor.
     *
     * @param QuizRepository $quizRepository Repozytorium quizów
     */
    public function __construct(private readonly QuizRepository $quizRepository)
    {
    }

    /**
     * Wyświetla ekran startowy quizu (brandowanie, opis, przycisk startowy).
     *
     * @param int $id ID quizu
     *
     * @return Response Odpowiedź HTTP z widokiem startowym quizu
     */
    #[Route('/quiz/{id}/start-view', name: 'app_quiz_start')]
    public function start(int $id): Response
    {
        $quiz = $this->quizRepository->find($id);

        if (!$quiz || !$quiz->isPublished()) {
            throw $this->createNotFoundException('Quiz nie istnieje lub nie został opublikowany.');
        }

        return $this->render('quiz/start_view.html.twig', [
            'quiz' => $quiz,
        ]);
    }
}
