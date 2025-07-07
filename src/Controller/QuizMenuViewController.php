<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuizMenuViewController extends AbstractController
{
    #[Route('/quizzes', name: 'app_quiz_list')]
    public function list(): Response
    {
        $quizzes = [
            ['id' => 1, 'title' => 'Quiz o Polsce'],
            ['id' => 2, 'title' => 'Historia Europy'],
            ['id' => 3, 'title' => 'Matematyka dla każdego'],
        ];

        return $this->render('quiz/menuView.html.twig', [
            'quizzes' => $quizzes,
        ]);
    }

    #[Route('/quiz/{id}', name: 'app_quiz_start')]
    public function start(int $id): Response
    {
        return $this->render('quiz/start.html.twig', [
            'quizId' => $id,
        ]);
    }
}
