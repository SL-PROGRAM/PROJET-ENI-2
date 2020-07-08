<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */
    public function index(CampusRepository $campusRepository,ParticipantRepository $participantRepository)
    {

        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
            'campuses'=>$campusRepository->findAll(),
            'participant'=>$participantRepository->findOneBy(['email'=>$this->getUser()->getUsername()])
        ]);
    }
}

