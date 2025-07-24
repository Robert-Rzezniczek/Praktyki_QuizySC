<?php
/*
 *  Quiz Menu View Controller
 */
namespace App\Controller;

use App\Repository\QuizRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\QuizResultRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
     * Wyświetla listę opublikowanych quizów użytkownika (nie admina),
     * wraz z informacją, czy zostały ukończone i jaki był wynik.
     *
     * @param QuizResultRepository $quizResultRepository Repozytorium wyników quizów
     *
     * @return Response Odpowiedź HTTP z widokiem listy quizów
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/quizzes', name: 'app_quiz_list')]
    public function list(QuizResultRepository $quizResultRepository): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('quiz_index');
        }

        $user = $this->getUser();
        $quizzes = $this->quizRepository->findBy(['isPublished' => true]);

        $quizData = [];

        foreach ($quizzes as $quiz) {
            $result = $quizResultRepository->findOneByQuizAndUser($quiz, $user);

            $quizData[] = [
                'id' => $quiz->getId(),
                'title' => $quiz->getTitle(),
                'completed' => $result !== null,
                'score' => $result?->getScore(), // Uwaga: wynik w skali 0.0–1.0 lub procent
            ];
        }

        return $this->render('quiz/menuView.html.twig', [
            'quizzes' => $quizData,
        ]);
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
