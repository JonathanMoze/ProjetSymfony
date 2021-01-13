<?php

namespace App\Controller;

use App\Entity\Series;
use App\Entity\Genre;
use App\Entity\Country;
use App\Entity\Rating;
use App\Form\RechercheFormType;
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

        $formData = ['Nom' => null];

        $form = $this->createForm(RechercheFormType::class, $formData);
        
        $form->handleRequest($requete);


        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
        }


        if($formData['Nom'] != ""){
            $rep = $this->getDoctrine()
            ->getRepository(Series::class);

            $donnees = $rep->createQueryBuilder('s')
                ->where('s.title LIKE :title')
                ->setParameter('title', '%'.$formData['Nom'].'%')
                ->orderBy('s.title', 'ASC')
                ->getQuery()
                ->getResult();
            
        }
        else{
            $donnees = $this->getDoctrine()
            ->getRepository(Series::class)
            ->findBy(
                array(),
                array('title' => 'ASC'),
            );
        }



        $seriesNotes = array();

        foreach($donnees as $serie){
            $rep = $this->getDoctrine()
            ->getRepository(Rating::class);

            $moyenne = $rep->createQueryBuilder('r')
            ->select("avg(r.value) as note")
            ->where("r.series = :id")
            ->setParameter('id', $serie->getId())
            ->getQuery()
            ->getResult();

            $seriesNotes[] = array(
                'serie' => $serie,
                'note' => number_format($moyenne[0]['note'], 1),
            );

        }

        $series=$paginator->paginate(
            $seriesNotes,
            $requete->query->getInt('page',1),
            10
        );

        return $this->render('series/liste_series.html.twig', [
            'formRecherche' => $form->createView(),
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

    

    

}
