<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeAdminController extends AbstractController
{
    /**
     * @Route("/commande/admin", name="commande_admin")
     */
    public function index(): Response
    {
        return $this->render('commande_admin/index.html.twig', [
            'controller_name' => 'CommandeAdminController',
        ]);
    }
}
