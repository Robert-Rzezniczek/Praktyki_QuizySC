<?php

/**
 * Faq service.
 */

namespace App\Service;

use App\Entity\Faq;
use App\Form\FaqType;
use App\Repository\FaqRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class FaqService.
 */
class FaqService
{
    private FormFactoryInterface $formFactory;
    private EntityManagerInterface $entityManager;
    private FaqRepository $faqRepository;

    /**
     * Constructor.
     *
     * @param FormFactoryInterface   $formFactory   FormFactoryInterface
     * @param EntityManagerInterface $entityManager EntityManagerInterface
     * @param FaqRepository          $faqRepository FaqRepository
     */
    public function __construct(FormFactoryInterface $formFactory, EntityManagerInterface $entityManager, FaqRepository $faqRepository)
    {
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->faqRepository = $faqRepository;
    }

    /**
     * Builds the faq form.
     *
     * @param Faq $faq Faq
     *
     * @return FormInterface FormInterface
     */
    public function buildForm(Faq $faq): FormInterface
    {
        return $this->formFactory->create(FaqType::class, $faq);
    }

    /**
     * Saves a new FAQ entry.
     *
     * @param Faq $faq Faq
     *
     * @return void void
     */
    public function save(Faq $faq): void
    {
        $this->entityManager->persist($faq);
        $this->entityManager->flush();
    }

    /**
     * Returns a list of FAQs sorted by item.
     *
     * @return array array
     */
    public function getAll(): array
    {
        return $this->faqRepository->findBy([], ['position' => 'ASC']);
    }
}
