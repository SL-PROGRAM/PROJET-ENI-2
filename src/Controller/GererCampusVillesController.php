<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GererCampusVillesController extends AbstractController
{
    /**
     * @Route("/gerer/villes", name="gerer_villes")
     */
    public function GererVille()
    {


        return $this->render('gerer_campus_villes/gererVille.html.twig', [
            'controller_name' => 'GererVillesController',
        ]);
    }

    /**
     * @Route("/gerer/campus", name="gerer_campus")
     */
    public function GererCampus()
    {


        return $this->render('gerer_campus_villes/gererCampus.html.twig', [
            'controller_name' => 'GererCampusController',
        ]);
    }
}
