<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Series;
use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Genre;
use App\Entity\Country;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class SeriesController extends AbstractController
{

    /**
     * @Route("/liste_series", name="series")
     */
    public function series(Request $requete, PaginatorInterface $paginator) {

        $donnees = $this->getDoctrine()
        ->getRepository(Series::class)
        ->findBy(
            array(),
            array('title' => 'ASC'),
        );

        $series=$paginator->paginate(
            $donnees,
            $requete->query->getInt('page',1),
            10
        );

        return $this->render('series/liste_series.html.twig', [
            'series' => $series,
        ]);
    }

    
    /**
     * @Route("/liste_series/{id}", name="poster_get")
     */
    public function poster(Series $serie) : Response
    {
        $poster = stream_get_contents($serie->getPoster());
        $rep = new Response($poster, 200,[
            "Content-type" => "image/jpeg",
        ]); 
        return $rep;
    }

    /**
     * @Route("/liste_genre", name="genre")
     */
    public function serie_genre() {

        $genres = $this->getDoctrine()
        ->getRepository(Genre::class)
        ->findBy(
            array(),
            array('name' => 'ASC'),
        );

        return $this->render('genre/liste_genre.html.twig', [
            'genres' => $genres,
        ]);
    }

     /**
     * @Route("/liste_pays", name="pays")
     */
    public function liste_pays() {

        $pays = $this->getDoctrine()
        ->getRepository(Country::class)
        ->findBy(
            array(),
            array('name' => 'ASC'),
        );

        return $this->render('pays/liste_pays.html.twig', [
            'pays' => $pays,
        ]);
    }

    /**
     * @Route("/pays/{id}", name="pays_serie")
     */
    public function pays_serie(Country $pays,Request $requete, PaginatorInterface $paginator) {

        $donnees = $pays->getSeries();

        $series=$series=$paginator->paginate(
            $donnees,
            $requete->query->getInt('page',1),
            10
        );
        return $this->render('pays/pays_serie.html.twig', [
            'pays' => $pays,
            'series' => $series,
        ]);
    }
    /**
     * @Route("/genre/{id}", name="genre_serie")
     */
    public function liste_genre(Genre $genre,Request $requete, PaginatorInterface $paginator) {

        $donnees = $genre->getSeries();

        $series=$series=$paginator->paginate(
            $donnees,
            $requete->query->getInt('page',1),
            10
        );
        return $this->render('genre/serie_genre.html.twig', [
            'genres' => $genre,
            'series' => $series,
        ]);
    }

    /**
     * @Route("/serie/{id}", name="saisons_serie")
     */
    public function serie(Series $serie)
    {
        $saisons = $this->getDoctrine()
        ->getRepository(Season::class)
        ->findBy(
            array('series' => $serie->getId()),
            array('number' => 'ASC'),
        );

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Episode::class);

        $episodes = array();
        foreach($saisons as $saison ){
            $episodes = array_merge($episodes, $repository->createQueryBuilder('e')
            ->where("e.season = ".$saison->getId())
            ->orderBy('e.number', 'ASC')
            ->getQuery()
            ->getResult());
        }


        return $this->render('series/serie.html.twig', [
            'serie' => $serie,
            'saisons' => $saisons,
            'episodes' => $episodes,
        ]);
    }

    /**
     * @Route("/saison/{id}", name="episode_saison")
     */
    public function saison(Season $season )
    {
        $episode = $this->getDoctrine()
        ->getRepository(Episode::class)
        ->findBy(
            array('season' => $season->getId()),
            array('number' => 'ASC'),
        );
        $serie = $season->getSeries();

        return $this->render('series/saison.html.twig', [
            'serie' => $serie,
            'saisons' => $season,
            'episodes' => $episode,
        ]);
    }

}
