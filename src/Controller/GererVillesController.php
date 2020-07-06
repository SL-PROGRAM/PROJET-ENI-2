<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GererVillesController extends AbstractController
{
    /**
     * @Route("/gerer/villes", name="gerer_villes")
     */
    public function index()
    {
        return $this->render('gerer_villes/index.html.twig', [
            'controller_name' => 'GererVillesController',
        ]);
    }
}
