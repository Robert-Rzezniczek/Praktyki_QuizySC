<?php

/**
 * Certificate controller.
 */

namespace App\Controller;

use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\QuizResultRepository;
use Twig\Environment;

/**
 * CertificateController class.
 */
class CertificateController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param QuizResultRepository $quizResultRepository QuizResultRepository
     */
    public function __construct(private readonly QuizResultRepository $quizResultRepository)
    {
    }

    /**
     * Generate certificate action.
     *
     * @param int         $id           int
     * @param Pdf         $knpSnappyPdf Pdf
     * @param Environment $twig         Environment
     *
     * @return Response Response
     */
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

    /**
     * Generate thank you action.
     *
     * @param int         $id           int
     * @param Pdf         $knpSnappyPdf Pdf
     * @param Environment $twig         Environment
     *
     * @return Response Response
     */
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
