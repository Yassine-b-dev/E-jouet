<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface as Session;
use App\Entity\Commande, App\Entity\Detail;
use App\Repository\JouetRepository;
use App\Repository\MembreRepository;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/panier")
 */
class PanierController extends AbstractController
{
    /**
     * @Route("/", name="panier")
     */
    public function index(Session $session, JouetRepository $jr)
    {
        $panier = $session->get("panier");
        $montant = 0;

        if(!empty($panier) ){
        foreach($panier as $ligne){
            $montant += $ligne["jouet"]->getPrix() * $ligne["quantite"];

            $jouet = $jr->find($ligne["jouet"]->getId());

            if($jouet = 0){

            }

        }
        }
        
        
        
        //dd($panier);


        return $this->render('panier/index.html.twig', ["total" => $montant, "panier" => $panier]);
    }

    /**
     * @Route("/paiement", name="paiement")
     */
    public function paiement(Session $session, JouetRepository $jr)
    {
        $panier = $session->get("panier");

        $utilisateur = $this->getUser();
        if(!$utilisateur){
            $this->addFlash("danger", "Vous devez vous connecter ou vous inscrire pour valider un panier !");
            return $this->redirectToRoute("panier");
        }
        $montant = 0;

        if(!empty($panier) ){
        foreach($panier as $ligne){
            $montant += $ligne["jouet"]->getPrix() * $ligne["quantite"];

            $jouet = $jr->find($ligne["jouet"]->getId());

        }
        }
        return $this->render('panier/paiement.html.twig', [ "utilisateur" => $utilisateur, "total" => $montant]);
    }

    /**
     * @Route("/ajouter/{id}", name="panier_ajouter", requirements={"id"="\d+"})
     */
    public function ajouter(Request $rq, Session $session, JouetRepository $jr, $id)
    {
        $panier = $session->get("panier", []); // le 2ième paramètre est renvoyé si "panier" n'existe pas dans la session
        $jouet = $jr->find($id);
        $qte = $rq->query->get("qte");
        $qte = empty($qte) ? 1 : $qte;
        $jouetExiste = false;
        if($jouet){
            foreach($panier as $indice => $ligne){
                if($jouet->getId() == $ligne["jouet"]->getId() ){
                    $totalDesire = $qte + $ligne["quantite"];
                    if ($totalDesire > $jouet->getStock()) {
                        $this->addFlash("danger", "Vous ne pouvez pas demander plus d'exemplaire que ce que nous avons en stock. Il se peut que vous ayez déjà des exemplaires de ce produit dans votre panier.");
                        return $this->redirectToRoute("detail_jouet", ["id" => $id]);
                    }else{
                    $qte += $ligne["quantite"];
                    $panier[$indice]["quantite"] = $qte;
                    $jouetExiste = true;
                    }
                    
                }
            }
            if(!$jouetExiste){
                $panier[] = [ "jouet" => $jouet, "quantite" => $qte ];
            }
            $this->addFlash("success", "Le jouet <strong>" . $jouet->getReference() . "</strong> a été ajouté au panier");
            $session->set("panier", $panier);
        }
        else{
            $this->addFlash("danger", "Le jouet n°$id n'existe pas");
        }
        return $this->redirectToRoute("boutique");
    }

    /**
     * @Route("/vider", name="panier_vider")
     */
    public function vider(Session $session)
    {
        $session->remove("panier");
        return $this->redirectToRoute("panier");
    }

    /**
     * @Route("/supprimer-jouet/{id}", name="panier_supprimer_jouet", requirements={"id"="\d+"})
     */
    public function supprimer(Session $session, $id)
    {
        $panier = $session->get("panier");
        foreach($panier as $indice => $ligne){
            if( $ligne["jouet"]->getId() == $id ){
                unset($panier[$indice]);
                break;
            }
        }
        $session->set("panier", $panier);
        return $this->redirectToRoute("panier");
    }

    /**
     * @Route("/modifier-quantite/{id}", name="panier_modifier_jouet", requirements={"id"="\d+"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function modifier(Request $rq, Session $session, JouetRepository $jr, $id)
    {
        $panier = $session->get("panier");
        $jouet = $jr->find($id);
        $qte = $rq->query->get("qte");
        $qte = empty($qte) ? 1 : $qte;
        foreach($panier as $indice => $ligne){
            if( $ligne["jouet"]->getId() == $id ){
                $panier[$indice]["quantite"] = $qte;
                break;
            }
        }

        $session->set("panier", $panier);
        $this->addFlash("success", "La quantité du jouet " . $jouet->getNomJouet() . " à bien été modifié" );
        return $this->redirectToRoute("panier");
    }

    /**
     * @Route("/valider", name="panier_valider")
     */
    public function valider(Session $session, EntityManager $em, JouetRepository $jr)
    {
        $panier = $session->get("panier");

        foreach($panier as $ligne){
            if ($ligne["jouet"]->getStock() < $ligne["quantite"]) {
                $jouet = $jr->find($ligne["jouet"]->getId());

                $this->addFlash("danger", "Il ne reste que " . $jouet->getStock() . " exemplaire(s) du Jouet " . $jouet->getNomJouet() );
                return $this->redirectToRoute("panier");
            }
        }
        $cmd = new Commande;
        $cmd->setMembre($this->getUser());
        $cmd->setDateEnregistrement(new \DateTime("now"));
        $cmd->setEtat("en attente");
        $montant = 0;
        foreach($panier as $ligne){
            $montant += $ligne["jouet"]->getPrix() * $ligne["quantite"];

            // ⚠ : il ne faut surtout pas utiliser $ligne["jouet"] dans setjouet 
            //      L'entity manager essaiera de créer un nouveau jouet bien que $ligne["jouet"] ait un id non nul
            //      Donc on récupère le jouet avec le JouetRepository
            $jouet = $jr->find($ligne["jouet"]->getId());

            $detail = new Detail;
            $detail->setCommande($cmd);
            $detail->setjouet($jouet);
            $detail->setQuantite($ligne["quantite"]);    
            $detail->setPrix($jouet->getPrix());
            
            // EXO : vérifier que la quantité commandée ne dépasse pas le stock
            //       sinon, réduire la quantité commandée (= stock)
            // 📣 Rappel : La méthode jouet::setStock a été modifiée
            
            $jouet->setStock( $jouet->getStock() - $ligne["quantite"] );
            $em->persist($detail);
        }
        $cmd->setMontant($montant);
        $em->persist($cmd);
        $em->flush();
        $session->remove("panier");
        $this->addFlash("success", "Votre commande a bien été enregistrée");
        return $this->redirectToRoute("boutique");
    }


    

}
