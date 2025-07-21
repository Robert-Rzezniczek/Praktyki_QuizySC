<?php

/**
 * Item controller.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class StartController.
 */
#[Route('/start')]
class StartController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param TranslatorInterface $translator Translator
     */
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index action.
     *
     * @return Response HTTP response
     */
    #[Route(
        name: 'start_index',
        methods: 'GET'
    )]
    public function index(): Response
    {
        return $this->render('start/index.html.twig');
    }
}
