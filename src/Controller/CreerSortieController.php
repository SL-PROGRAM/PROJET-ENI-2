<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CreerSortieController extends AbstractController
{
    /**
     * @Route("/creer/sortie", name="creer_sortie")
     */
    public function index()
    {
        return $this->render('creer_sortie/index.html.twig', [
            'controller_name' => 'CreerSortieController',
        ]);
    }
}
