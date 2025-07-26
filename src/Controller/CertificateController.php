<?php

namespace App\Controller;

use App\Repository\UserProfileRepository;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\QuizResultRepository;
use Twig\Environment;

class CertificateController extends AbstractController
{
    public function __construct(private readonly QuizResultRepository $quizResultRepository)
    {
    }

    #[Route('/certyfikat/{id}', name: 'quiz_certificate')]
    public function generate(int $id, Pdf $knpSnappyPdf, Environment $twig): Response
    {
        $result = $this->quizResultRepository->find($id);

        if (!$result || $result->getUser() !== $this->getUser()) {
            throw $this->createNotFoundException('Nie znaleziono wyników');
        }

        $user = $this->getUser();
        $userProfile = $user->getProfile();

        $quiz = $result->getQuiz();

        $html = $this->renderView('pdf/certificate_qualified.html.twig', [
            'user' => $userProfile,
            'score' => $result->getScore(),
            'quiz' => $quiz,
        ]);

        $pdfContent = $knpSnappyPdf->getOutputFromHtml($html);

        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="certyfikat.pdf"',
        ]);
    }

    #[Route('/podziekowanie/{id}', name: 'quiz_thankyou_certificate')]
    public function generateThankYou(int $id, Pdf $knpSnappyPdf, Environment $twig): Response
    {
        $result = $this->quizResultRepository->find($id);

        if (!$result || $result->getUser() !== $this->getUser()) {
            throw $this->createNotFoundException('Nie znaleziono wyników');
        }

        $user = $this->getUser();
        /** @var \App\Entity\UserAuth $user */
        $userProfile = $user->getProfile();
        $quiz = $result->getQuiz();

        $html = $this->renderView('pdf/certificate_thankyou.html.twig', [
            'user' => $userProfile,
            'score' => $result->getScore(),
            'quiz' => $quiz,
        ]);

        $pdfContent = $knpSnappyPdf->getOutputFromHtml($html);

        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="potwierdzenie_udzialu.pdf"',
        ]);
    }
}
