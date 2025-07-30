<?php

/**
 * Item controller.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class StartController.
 */
#[Route('/start')]
class StartController extends AbstractController
{
    /**
     * Constructor.
     */
    public function __construct()
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
