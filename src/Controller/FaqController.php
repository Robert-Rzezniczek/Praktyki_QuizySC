<?php

namespace App\Controller;

use App\Entity\Faq;
use App\Form\FaqType;
use App\Repository\FaqRepository; // ← TO MUSI BYĆ
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



/**
     * Class FaqController.
     */
    class FaqController extends AbstractController
    {
        /**
         * Wyświetla stronę FAQ.
         *
         * @param FaqRepository $faqRepository Repozytorium FAQ
         *
         * @return Response
         */
        #[Route('/faq', name: 'app_faq')]
        public function index(FaqRepository $faqRepository): Response
        {
            $faqs = $faqRepository->findBy([], ['position' => 'ASC']);

            return $this->render('faq/index.html.twig', [
                'faqs' => $faqs,
            ]);
        }
        #[Route('/faq/new', name: 'app_faq_new')]
        public function new(Request $request, EntityManagerInterface $em): Response
        {
            $faq = new Faq();
            $form = $this->createForm(FaqType::class, $faq);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($faq);
                $em->flush();

                return $this->redirectToRoute('app_faq');
            }

            return $this->render('faq/new.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }
