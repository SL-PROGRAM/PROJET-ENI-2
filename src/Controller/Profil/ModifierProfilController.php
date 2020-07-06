<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ModifierProfilController extends AbstractController
{
    /**
     * @Route("/modifier/profil", name="modifier_profil")
     */
    public function index()
    {
        return $this->render('modifier_profil/index.html.twig', [
            'controller_name' => 'ModifierProfilController',
        ]);
    }
}
