<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AfficherProfilController extends AbstractController
{
    /**
     * @Route("/afficher/profil", name="afficher_profil")
     */
    public function index()
    {
        return $this->render('afficher_profil/index.html.twig', [
            'controller_name' => 'AfficherProfilController',
        ]);
    }
}
