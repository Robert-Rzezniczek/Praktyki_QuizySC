<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AboutController extends AbstractController
{
    /**
     * O nas (About Us) page.
     */
    #[Route('/o-nas', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('about/about.html.twig');
    }
}
