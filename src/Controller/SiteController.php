<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    /**
     * @Route("/site", name="site")
     */
    public function index(): Response
    {
        return $this->render('site/index.html.twig', [
            'controller_name' => 'SiteController',
        ]);
    }
    
    /**
     * @Route("/", name="home")
     */
    public function home() {
        return $this->render('site/home.html.twig');
    }

    /**
     * @Route("/apropos", name="apropos")
     */
    public function apropos() {
        return $this->render('site/apropos.html.twig');
    }

    /**
     * @Route("/series", name="series")
     */
    public function series() {
        return $this->render('site/series.html.twig');
    }
}
