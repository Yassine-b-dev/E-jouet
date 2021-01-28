<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommandeRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

/**
     * @Route("/admin")
     */
class CommandeAdminController extends AbstractController
{
    /**
     * @Route("/commande", name="commande_admin")
     */
    public function index(CommandeRepository $cr): Response
    {
        return $this->render('commande_admin/index.html.twig', [
            'commandes' =>  $cr->findAll(),
        ]);
    }

    /**
     * @Route("/commande/modifierEtat/{id}", name="modifier_commande")
     */
    public function modifierEtat(EntityManagerInterface $em, Request $request, CommandeRepository $cr, $id): Response
    {

        $commandeAmodifier = $cr->find($id);

        if( $request->isMethod("POST") ){
            $etat = $request->request->get("etat");   // je récupère la valeur de l'input 'titre' du formulaire
            
            if(!empty($etat)){
                $commandeAmodifier->setEtat($etat);
                $em->flush();
                $this->addFlash("success", "L'état de la commande n°$id a bien été modifié");
                return $this->redirectToRoute("commande_admin");
            } 
            
        }
        return $this->render('commande_admin/commandemodif.html.twig', [
            'commande' =>  $commandeAmodifier,
        ]);
        }
}
