<?php

namespace App\Controller;

use App\Entity\Jouet;
use App\Form\JouetType;
use App\Repository\JouetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/jouet")
 */
class JouetController extends AbstractController
{
    /**
     * @Route("/", name="jouet_index", methods={"GET"})
     */
    public function index(JouetRepository $jouetRepository): Response
    {
        return $this->render('jouet/index.html.twig', [
            'jouets' => $jouetRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="jouet_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $jouet = new Jouet();
        $form = $this->createForm(JouetType::class, $jouet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($jouet);
            $entityManager->flush();

            return $this->redirectToRoute('jouet_index');
        }

        return $this->render('jouet/new.html.twig', [
            'jouet' => $jouet,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="jouet_show", methods={"GET"})
     */
    public function show(Jouet $jouet): Response
    {
        return $this->render('jouet/show.html.twig', [
            'jouet' => $jouet,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="jouet_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Jouet $jouet): Response
    {
        $form = $this->createForm(JouetType::class, $jouet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('jouet_index');
        }

        return $this->render('jouet/edit.html.twig', [
            'jouet' => $jouet,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="jouet_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Jouet $jouet): Response
    {
        if ($this->isCsrfTokenValid('delete'.$jouet->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($jouet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('jouet_index');
    }
}
