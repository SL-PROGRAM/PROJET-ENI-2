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
 * Gestion des campus
 * @Route("/admin/campus")
 */
class CampusController extends AbstractController
{
    /**
     * @param CampusRepository $campusRepository
     * @param Request $request
     * @return Response
     * @Route("/", name="campus_index", methods={"GET", "POST"})
     */
    public function index(CampusRepository $campusRepository, Request $request): Response
    {
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
                'addForm' => $form->createView()
        ]);
    }

    /**
     * @param CampusRepository $campusRepository
     * @param Request $request
     * @param Campus $campus
     * @return Response
     * @Route("/{id}/edit", name="campus_edit", methods={"GET","POST"})
     */
    public function edit(CampusRepository $campusRepository, Request $request, Campus $campus): Response
    {
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('campus_index');
        }

        return $this->render('campus/index.html.twig', [
            'campuses' => $campusRepository->findAll(),
            'campus' => $campus,
            'editForm' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Campus $campus
     * @return Response
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
