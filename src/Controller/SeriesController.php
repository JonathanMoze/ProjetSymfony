<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SeriesController extends AbstractController
{
    /**
     * @Route("/series", name="series")
     */
    public function index(): Response
    {
        return $this->render('series/index.html.twig', [
            'controller_name' => 'SeriesController',
        ]);
    }
}
