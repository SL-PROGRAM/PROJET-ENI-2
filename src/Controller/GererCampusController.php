<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GererCampusController extends AbstractController
{
    /**
     * @Route("/gerer/campus", name="gerer_campus")
     */
    public function index()
    {
        return $this->render('gerer_campus/index.html.twig', [
            'controller_name' => 'GererCampusController',
        ]);
    }
}
