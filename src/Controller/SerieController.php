<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Series;
use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Rating;
use App\Form\RatingFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;





class SerieController extends AbstractController
{

    /**
     * @Route("/serie/{id}", name="info_serie")
     */
    public function serie(Request $request, Series $serie)
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

        $ratings = $this->getDoctrine()
            ->getRepository(Rating::class)
            ->findBy(
                array('series' => $serie->getId()),
                array('value' => 'ASC'),
                );


        $rep = $this->getDoctrine()
        ->getRepository(Rating::class);

        $moyenne = $rep->createQueryBuilder('r')
        ->select("avg(r.value) as note")
        ->where("r.series = :id")
        ->setParameter('id', $serie->getId())
        ->getQuery()
        ->getResult();

        $note = number_format($moyenne[0]['note'], 1);
        
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if($user != "anon."){
            
            $rating = $this->getDoctrine()
            ->getRepository(Rating::class)
            ->findOneBy(
                array('series' => $serie->getId(), 'user' => $user->getId()),
            );

            if( $rating == null){
                $rating = new Rating();
            }
        }
        else{
            $rating = new Rating();
        }
             
        


        
        

        $form = $this->createForm(RatingFormType::class, $rating);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            if(!$rating->getId()){
                $rating->setUser($user);
                $rating->setSeries($serie);
                

                
            }
            $rating->setDate(new \DateTime('now'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($rating);
            $entityManager->flush();
        }






        return $this->render('serie/serie.html.twig', [
            'serie' => $serie,
            'saisons' => $saisons,
            'episodes' => $episodes,
            'ratings' => $ratings,
            'note' => $note,
            'formRating' => $form->createView(),
            'editMode' => $rating->getId() !== null,
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

        return $this->render('serie/saison.html.twig', [
            'serie' => $serie,
            'saisons' => $season,
            'episodes' => $episode,
        ]);
    }
}
