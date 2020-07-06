<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AnnulerSortieController extends AbstractController
{
    /**
     * @Route("/annuler/sortie", name="annuler_sortie")
     */
    public function index()
    {
        return $this->render('annuler_sortie/index.html.twig', [
            'controller_name' => 'AnnulerSortieController',
        ]);
    }
}
