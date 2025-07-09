<?php

namespace App\Service;

use App\Entity\Faq;
use App\Form\FaqType;
use App\Repository\FaqRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class FaqService
{
    private FormFactoryInterface $formFactory;
    private EntityManagerInterface $entityManager;
    private FaqRepository $faqRepository;

    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        FaqRepository $faqRepository,
    ) {
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->faqRepository = $faqRepository;
    }

    /**
     * Tworzy i przetwarza formularz dodawania FAQ.
     */
    public function buildForm(Faq $faq): FormInterface
    {
        return $this->formFactory->create(FaqType::class, $faq);
    }

    /**
     * Zapisuje nowy wpis FAQ.
     */
    public function save(Faq $faq): void
    {
        $this->entityManager->persist($faq);
        $this->entityManager->flush();
    }

    /**
     * Zwraca listę FAQ posortowaną wg pozycji.
     */
    public function getAll(): array
    {
        return $this->faqRepository->findBy([], ['position' => 'ASC']);
    }
}
