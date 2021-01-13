<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\RechercheFormType;
use App\Entity\Series;
use App\Entity\Rating;

class PopulariteController extends AbstractController
{
    /**
     * @Route("/series_popularite", name="series_pop")
     */
    public function series_pop(Request $requete, PaginatorInterface $paginator) {

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
        $rep = $this->getDoctrine()
        ->getRepository(Rating::class);

        foreach($donnees as $serie){
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
        $note = array_column($seriesNotes, 'note');
        array_multisort($note, SORT_DESC, $seriesNotes);

        $series=$paginator->paginate(
            $seriesNotes,
            $requete->query->getInt('page',1),
            10
        );

        return $this->render('popularite/series_popularite.html.twig', [
            'formRecherche' => $form->createView(),
            'series' => $series,
        ]);
    }
}
