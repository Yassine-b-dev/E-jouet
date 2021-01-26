<?php

namespace App\Controller;

use App\Entity\Jouet;
use App\Form\JouetType;
use App\Repository\JouetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("admin/jouet")
 */
class JouetController extends AbstractController
{
    /**
     * @Route("/", name="jouet_index")
     */
    public function index(JouetRepository $jouetRepository): Response
    {
        $liste_jouets = $jouetRepository->findAll();
        return $this->render('jouet/index.html.twig', [
            'jouets' => $liste_jouets,
        ]);
    }

    /**
     * @Route("/ajouter", name="jouet_ajouter")
     */
    public function nouveau(Request $request, EntityManagerInterface $em): Response
    {
        $jouet = new Jouet;
        $formJouet = $this->createForm(JouetType::class, $jouet);
        $formJouet->handleRequest($request);
        if($formJouet->isSubmitted() && $formJouet->isValid())
        {
            if($fichier = $formJouet->get("photo")->getData())
            {
                $destination = $this->getParameter("dossier_images");
                $nomFichier = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                $nouveauNom = str_replace(" ", "_", $nomFichier);
                $nouveauNom .= "_" . uniqid() . "." . $fichier->guessExtension();
                $fichier->move($destination, $nouveauNom);
                $jouet->setPhoto($nouveauNom);
            }
            $em->persist($jouet);
            $em->flush();
            $this->addFlash("success", "Le nouveau jouet a bien été ajouté");
            return $this->redirectToRoute("jouet_index");
        }

        return $this->render('jouet/ajouter.html.twig', ['formJouet' => $formJouet->createView()]);
    }

    /**
     * @Route("/{id}", name="jouet_show", methods={"GET"})
     */
    public function show(Jouet $jouet): Response
    {
        return $this->render('jouet/fiche.html.twig', [
            'jouet' => $jouet,
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="jouet_modifier")
     */
    public function modifier(Request $request,EntityManagerInterface $em,JouetRepository $jr, $id): Response
    {
        $jouet = $jr->find($id);
        $formJouet = $this->createForm(JouetType::class, $jouet);
        $formJouet->handleRequest($request);
        if($formJouet->isSubmitted() && $formJouet->isValid())
        {
            if($fichier = $formJouet->get("photo")->getData())
            {
                $destination = $this->getParameter("dossier_images");
                $nomFichier = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                $nouveauNom = str_replace(" ", "_", $nomFichier);
                $nouveauNom .= "_" . uniqid() . "." . $fichier->guessExtension();
                $fichier->move($destination, $nouveauNom);
                $jouet->setPhoto($nouveauNom);
            }
            $em->persist($jouet);
            $em->flush();
            $this->addFlash("success", "Le jouet a bien été modifié");
            return $this->redirectToRoute("jouet_index");
        }

        return $this->render('jouet/formulaire.html.twig', [
            'jouet' => $jouet,
            'formJouet' => $formJouet->createView(),
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="jouet_supprimer")
     */
    public function supprimer(JouetRepository $jouetRepository,Request $request, EntityManagerInterface $em,$id): Response
    {
        $jouetAsupprimer = $jouetRepository->find($id);
        if($request->isMethod("POST"))
        {
            $em->remove($jouetAsupprimer);
            $em->flush();
            $this->addFlash("success","Le jouet n°$id a bien été supprimé");
            return $this->redirectToRoute("jouet_index");
            
        }
        return $this->render("jouet/supprimer.html.twig", ["jouet" => $jouetAsupprimer]);
    }
    /**
     * @Route("/fiche/{id}", name="jouet_fiche")
     */
    public function fiche(jouetRepository $jouetRepository, $id)
    {
        $jouet = $jouetRepository->find($id);
        return $this->render("jouet/fiche.html.twig",["jouet" => $jouet]);
    }
}
