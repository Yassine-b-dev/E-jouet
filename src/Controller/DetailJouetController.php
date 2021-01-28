<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Jouet;
use App\Repository\JouetRepository;

class DetailJouetController extends AbstractController
{
    /**
     * @Route("/detail/jouet/{id}", name="detail_jouet")
     */
    public function index(JouetRepository $jr, $id): Response
    {

        
        return $this->render('detail_jouet/index.html.twig', [
            'jouet' =>  $jr->find($id),
        ]);
    }
}
