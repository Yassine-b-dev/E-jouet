<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\JouetRepository;
use App\Repository\MembreRepository;
use App\Repository\CommandeRepository;
use App\Repository\DetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class MonCompteController extends AbstractController
{
    /**
     * @Route("/mon_compte", name="mon_compte")
     */
    public function index(): Response
    {
        return $this->render('mon_compte/index.html.twig');
    }
    /**
     * @Route("/commande/{id}", name="commande")
     */
    public function affichageCommande(CommandeRepository $cr,DetailRepository $dr,$id)
    {
        $utilisateur = $this->getUser();
        $commande = $cr->find($id);
        if($utilisateur->getId() == $commande->getMembre()->getId())
        {
            return $this->render('mon_compte/commande.html.twig', ['commande' => $commande]);
        }
        else
        {
            $this->addFlash("danger", "Vous n'avez pas accès à cette commande !");
            return $this->redirectToRoute("mon_compte");
        }    
    }
  
}
