<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ville")
 */
class VilleController extends AbstractController
{
    /**
     * @Route("/", name="ville_index", methods={"GET","POST"})
     */
    public function index(VilleRepository $villeRepository, Request $request): Response
    {
        $ville = new Ville();
        $addForm = $this->createForm(VilleType::class, $ville);
        $addForm->handleRequest($request);

        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ville);
            $entityManager->flush();

            return $this->redirectToRoute('ville_index');
        }

        return $this->render('ville/index.html.twig', [
            'villes' => $villeRepository->findAll(),
            'addForm' => $addForm->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="ville_edit", methods={"GET","POST"})
     */
    public function edit(VilleRepository $villeRepository, Request $request, Ville $ville): Response
    {
        $editForm = $this->createForm(VilleType::class, $ville);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ville_index');
        }

        return $this->render('ville/index.html.twig', [
            'villes' => $villeRepository->findAll(),
            'ville' => $ville,
            'editForm' => $editForm->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ville_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Ville $ville): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ville->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ville);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ville_index');
    }
}
