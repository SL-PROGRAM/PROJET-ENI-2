<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ModifierSortieController extends AbstractController
{
    /**
     * @Route("/modifier/sortie", name="modifier_sortie")
     */
    public function index()
    {
        return $this->render('modifier_sortie/index.html.twig', [
            'controller_name' => 'ModifierSortieController',
        ]);
    }
}
