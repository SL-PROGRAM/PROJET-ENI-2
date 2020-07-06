<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AfficherSortieController extends AbstractController
{
    /**
     * @Route("/afficher/sortie", name="afficher_sortie")
     */
    public function index()
    {
        return $this->render('afficher_sortie/index.html.twig', [
            'controller_name' => 'AfficherSortieController',
        ]);
    }
}
