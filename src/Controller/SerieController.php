<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Series;
use App\Entity\Season;
use App\Entity\Rating;
use App\Form\RatingFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;





class SerieController extends AbstractController
{

    /**
     * @Route("/serie/{id}", name="info_serie")
     */
    public function serie(Request $request, Series $serie)
    {
        

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

        return $this->render('serie/saison.html.twig', [
            'saison' => $season,
        ]);
    }
}
