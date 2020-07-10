<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\SortieParticipant;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieParticipantRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie")
 */
class SortieController extends AbstractController
{
    /**
     * @Route("/", name="sortie_index", methods={"GET"})
     */
    public function index(SortieRepository $sortieRepository): Response
    {

        return $this->render('sortie/index.html.twig', [
            'sorties' => $sortieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="sortie_new", methods={"GET","POST"})
     */
    public function new(Request $request,
                        EtatRepository $etatRepository,
                        ParticipantRepository $participantRepository,
                        LieuRepository $lieuRepository): Response
    {

        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /*
             * remplir valeur manquant
             */

            $etat = $etatRepository->findOneBy(['libelle' => 'Créée']);
            $sortie->setEtat($etat);
            $organisateur = $participantRepository->findOneBy(['email' => $this->getUser()->getUsername()]);
            $sortie->setOrganisateur($organisateur);
            $etat = $etatRepository->findOneBy(['libelle' => 'Créée']);
            $sortie->setEtat($etat);
            $sortie->setCampus($organisateur->getCampus());




            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();

//            if ($form->get('sauvegarder')->isClicked()) {
//                $etat = $etatRepository->findOneBy(['libelle' => 'Créée']);
//                $sortie->setEtat($etat);
//            }
//            else{
//                $etat = $etatRepository->findOneBy(['libelle' => 'Ouverte']);
//                $sortie->setEtat($etat);
//            }

            return $this->redirectToRoute('sortie_index');
        }

        return $this->render('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="sortie_show", methods={"GET"})
     */
    public function show(Sortie $sortie): Response
    {
        //calculer de durée en jour et heur
        $dureeSeconde = $sortie->getDuree();
        $dureeJour = $dureeSeconde / (3600*24);
        $dureeSeconde = $dureeSeconde % (3600*24);
        $dureeHour = $dureeSeconde / (3600);


        return $this->render('sortie/show.html.twig', [
            'dureeJour' => $dureeJour,
            'dureeHour' => $dureeHour,
            'sortie' => $sortie,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sortie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Sortie $sortie): Response
    {

        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sortie_index');
        }

        return $this->render('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="sortie_delete", methods={"GET", "POST"})
     */
    public function delete(Request $request, Sortie $sortie, EtatRepository $er): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setInfosSortie('ANNULATION :'.$sortie->getInfosSortie());
            $sortie->setEtat($er->findOneBy(['libelle'=>'Annulée']));
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('accueil');
        }

        return $this->render('sortie/annuler.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/inscrire/{id}", name="sortie_inscrire", methods={"GET"})
     */
    public function inscrire(Request $request, Sortie $sortie, SortieRepository $sr, int $id): Response
    {
        $sortieParticipant= new SortieParticipant();
        $sortieParticipant->setSortie($sr->find($request->get('id')));
        $sortieParticipant->setParticipant($this->getUser());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($sortieParticipant);
        $entityManager->flush();
        return $this->redirectToRoute('accueil');
    }

    /**
     * @Route("/desinscrire/{id}", name="sortie_desinscrire", methods={"GET"})
     */
    public function desinscrire(Request $request, Sortie $sortie,SortieParticipantRepository $sr, int $id): Response
    {
        $sortieParticipant=$sr->findOneBy(['sortie'=>$id, 'participant'=>$this->getUser()]);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($sortieParticipant);
        $entityManager->flush();
        return $this->redirectToRoute('accueil');
    }
    /**
     * @Route("/publier/{id}", name="sortie_publier", methods={"GET"})
     */
    public function publier(Request $request, Sortie $sortie,SortieRepository $sr, EtatRepository $er, int $id): Response
    {
        $sortie=$sr->find($id);
        $sortie->setEtat($er->findOneBy(['libelle'=>'Ouverte']));
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('accueil');
    }
}
