<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/campus")
 */
class CampusController extends AbstractController
{
    /**
     * @Route("/", name="campus_index", methods={"GET", "POST"})
     */
    public function index(CampusRepository $campusRepository, Request $request): Response
    {
        //Copier la fonction new ici
        //Passer le form en parametre
        //Récupérer le form en twig

        $campus = new Campus();
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($campus);
            $entityManager->flush();
            return $this->redirectToRoute('campus_index');
        }

            return $this->render('campus/index.html.twig', [
            'campuses' => $campusRepository->findAll(),
                'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/new", name="campus_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $campus = new Campus();
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($campus);
            $entityManager->flush();

            return $this->redirectToRoute('campus_index');
        }

        return $this->render('campus/new.html.twig', [
            'campus' => $campus,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="campus_show", methods={"GET"})
     */
    public function show(Campus $campus): Response
    {
        return $this->render('campus/show.html.twig', [
            'campus' => $campus,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="campus_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Campus $campus): Response
    {
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('campus_index');
        }

        return $this->render('campus/edit.html.twig', [
            'campus' => $campus,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="campus_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Campus $campus): Response
    {
        if ($this->isCsrfTokenValid('delete'.$campus->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($campus);
            $entityManager->flush();
        }

        return $this->redirectToRoute('campus_index');
    }
}
