<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\JouetRepository;
use App\Entity\Jouet;

class BoutiqueController extends AbstractController
{
    /**
     * @Route("/boutique", name="boutique")
     */
    public function index(JouetRepository $jouetRepository): Response
    {
        $liste_jouets = $jouetRepository->findAll(); 
        return $this->render('boutique/index.html.twig', [
            'jouets' => $liste_jouets,
        ]);
    }
}
