<?php

namespace App\Controller;

use App\Data\Search;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\SortieParticipant;
use App\Form\EditSortieType;
use App\Form\FiltreSortieType;
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
 * Class SortieController, Gestion des sorties, y compris de la page d'accueil
 * @package App\Controller
 */
class SortieController extends AbstractController
{
    /**
     * @param SortieRepository $sortieRepository
     * @param Request $request
     * @param ParticipantRepository $participantRepository
     * @return Response
     * @Route("/", name="accueil", methods={"GET","POST"})
     */
    public function index(SortieRepository $sortieRepository, Request $request, ParticipantRepository $participantRepository): Response
    {
        $data = new Search();
        $form = $this->createForm(FiltreSortieType::class, $data);
        $participant= $participantRepository->findOneBy(['email' => $this->getUser()->getUsername()]);
        $form->handleRequest($request);
        if($form->isSubmitted()) {
            $data=$form->getData();
            $sorties = $sortieRepository->findSearch($data,$participant);
        }else{
            $sorties=$sortieRepository->findAll();
        }
        return $this->render('sortie/index.html.twig', [
            'sorties'=>$sorties,
            'form'=>$form->createView(),
            'participant'=> $participant
        ]);
    }

    /**
     * @param Request $request
     * @param ParticipantRepository $participantRepository
     * @param EtatRepository $etatRepository
     * @return Response
     * @Route("/sortie/new", name="sortie_new", methods={"GET","POST"})
     */
    public function new(Request $request, ParticipantRepository $participantRepository, EtatRepository $etatRepository): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);
        $organisateur = $participantRepository->findOneBy(['email' => $this->getUser()->getUsername()]);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setOrganisateur($organisateur);
            $sortie->setCampus($organisateur->getCampus());
            $sortieParticipant = new SortieParticipant();
            $sortieParticipant->setParticipant($organisateur);
            $sortieParticipant->setSortie($sortie);
            $sortie->addSortieParticipant($sortieParticipant);

            $entityManager = $this->getDoctrine()->getManager();

            if ($sortie->getLieu() == null){
                $sortieLIeu = ($request->get("sortie"));
                $lieu = new Lieu();
                $lieu->setNom($sortieLIeu['lieu']['nom']);
                $lieu->setVille($sortie->getVille());
                $lieu->setRue($sortieLIeu['lieu']['rue']);
                $lieu->setLongitude($sortieLIeu['lieu']['latitude']);
                $lieu->setLatitude($sortieLIeu['lieu']['longitude']);

                $entityManager->persist($lieu);
                $sortie->setLieu($lieu);            }

            if ($form->get('Enregistrer') === $form->getClickedButton() ){
               $sortie->setEtat($etatRepository->findOneBy(['libelle' => "Créée"]));
            }
            if ($form->get('Publier') === $form->getClickedButton() ){
                $sortie->setEtat($etatRepository->findOneBy(['libelle' => "Ouverte"]));
            }

            $entityManager->persist($sortieParticipant);
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('accueil');
        }

        return $this->render('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
            'new' => $organisateur,
        ]);
    }

    /**
     * @param Sortie $sortie
     * @return Response
     * @Route("/sortie/{id}", name="sortie_show", methods={"GET"})
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
     * @param Request $request
     * @param Sortie $sortie
     * @param EtatRepository $etatRepository
     * @return Response
     * @Route("/sortie/{id}/edit", name="sortie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Sortie $sortie,
                         EtatRepository $etatRepository,
                         LieuRepository $lieuRepository): Response
    {

        $lieu = $sortie->getLieu();
        $sortie->setVille($lieu->getVille());
        $form = $this->createForm(EditSortieType::class, $sortie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            if ($sortie->getLieu() !== $lieu){
                $sortieLIeu = ($request->get("sortie"));
                $newLieu = new Lieu();
                $newLieu->setNom($sortieLIeu['lieu']['nom']);
                $newLieu->setVille($sortie->getVille());
                $newLieu->setRue($sortieLIeu['lieu']['rue']);
                $newLieu->setLongitude($sortieLIeu['lieu']['latitude']);
                $newLieu->setLatitude($sortieLIeu['lieu']['longitude']);

                $entityManager->persist($lieu);
                $sortie->setLieu($lieu);
            $sortie->setLieu($lieu);
        } else{
            dump($request);
            $sortie->getLieu();
        }


            if ($form->get('Enregistrer') === $form->getClickedButton() ){
                $sortie->setEtat($etatRepository->findOneBy(['libelle' => "Créée"]));
            }
            if ($form->get('Publier') === $form->getClickedButton() ){
                $sortie->setEtat($etatRepository->findOneBy(['libelle' => "Ouverte"]));
            }

            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('accueil');
        }

        return $this->render('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Sortie $sortie
     * @param EtatRepository $er
     * @return Response
     * @Route("/sortie/{id}/delete", name="sortie_delete", methods={"GET", "POST"})
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
     * Inscription de l'utilisateur à une sortie
     * @param Request $request
     * @param Sortie $sortie
     * @param SortieRepository $sr
     * @param int $id
     * @return Response
     * @Route("/sortie/inscrire/{id}", name="sortie_inscrire", methods={"GET"})
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
     * Désistement
     * @param Request $request
     * @param Sortie $sortie
     * @param SortieParticipantRepository $sr
     * @param int $id
     * @return Response
     * @Route("/sortie/desinscrire/{id}", name="sortie_desinscrire", methods={"GET"})
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
     * Publier sa sortie
     * @param Request $request
     * @param Sortie $sortie
     * @param SortieRepository $sr
     * @param EtatRepository $er
     * @param int $id
     * @return Response
     * @Route("/sortie/publier/{id}", name="sortie_publier", methods={"GET"})
     */
    public function publier(Request $request, Sortie $sortie,SortieRepository $sr, EtatRepository $er, int $id): Response
    {
        $sortie=$sr->find($id);
        $sortie->setEtat($er->findOneBy(['libelle'=>'Ouverte']));
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('accueil');
    }

}
