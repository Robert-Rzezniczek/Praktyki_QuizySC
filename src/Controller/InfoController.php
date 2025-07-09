<?php

namespace App\Controller;

use App\Entity\Faq;
use App\Form\FaqType;
use App\Repository\FaqRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InfoController extends AbstractController
{
    #[Route('/faq', name: 'app_faq')]
    public function faq(Request $request, FaqRepository $faqRepository, EntityManagerInterface $em): Response
    {
        $faqs = $faqRepository->findBy([], ['position' => 'ASC']);
        $form = null;

        if ($this->isGranted('ROLE_ADMIN')) {
            $faq = new Faq();
            $form = $this->createForm(FaqType::class, $faq);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($faq);
                $em->flush();

                $this->addFlash('success', 'Dodano pytanie do FAQ.');

                return $this->redirectToRoute('app_faq');
            }
        }

        return $this->render('info/faq.html.twig', [
            'faqs' => $faqs,
            'form' => $form?->createView(),
        ]);
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('info/about.html.twig');
    }

    #[Route('/rules', name: 'app_rules')]
    public function rules(): Response
    {
        return $this->render('info/rules.html.twig');
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('info/contact.html.twig');
    }
}
